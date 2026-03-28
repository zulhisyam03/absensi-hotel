<?php

namespace App\Http\Controllers\tables;

use App\Http\Controllers\Controller;
use App\Models\ShiftKerja;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absen;
use App\Models\Pegawai;
use App\Models\Param;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DataTableController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    //
    $user = Auth::user();
    $role = $user->pegawai->jabatan;
    $departemen = $user->pegawai->departemen;

    if ($request->ajax()) {
      $absensi = Absen::query()
        ->select('absens.*', 'pegawais.nama_pegawai', 'pegawais.departemen') // Pilih semua kolom dari absens
        ->leftJoin('pegawais', 'absens.no_pegawai', '=', 'pegawais.no_pegawai')

        // ⭐️ 2. Lakukan pengurutan berdasarkan kolom relasi
        ->orderBy('absens.created_at', direction: 'DESC') // Urutkan berdasarkan nama_pegawai A-Z
        ->with('pegawai'); // Pertahankan Eager Loading untuk kolom data

      // 🔥 Logika Filter Berdasarkan Jabatan dan Departemen
      if ((strtolower($role) != 'hr') && strtolower($role) != 'hotel manager') {
        if ((strtolower($role) == 'supervisor') || (strtolower($role) == 'dept head b')) {
          // Jika Supervisor → tampilkan semua dalam departemen yang sama
          $absensi->where('pegawais.departemen', $departemen);
        } else {
          // Selain itu → tampilkan yang jabatan sama
          $absensi->where('pegawais.no_pegawai', $user->pegawai->no_pegawai);
        }
      }

      return DataTables::of($absensi)
        // ⭐️ Tambahkan ini untuk membuat kolom penomoran otomatis
        ->addIndexColumn()
        ->addColumn('nama_pegawai', function ($row) {
          $nama = e($row->pegawai ? $row->pegawai->nama_pegawai : 'N/A');
          $pictIn = $row->pict_in ? asset('storage/' . $row->pict_in) : '';
          $pictOut = $row->pict_out ? asset('storage/' . $row->pict_out) : '';

          return '<a href="#"
                class="btn-show-foto"
                data-id="' . $row->id . '"
                data-nama="' . strtoupper($nama) . '"
                data-pict-in="' . $pictIn . '"
                data-pict-out="' . $pictOut . '"
            >' . $nama . '</a>';
        })
        ->addColumn('departemen', function ($row) {
          return $row->pegawai ? $row->pegawai->departemen : 'N/A';
        })
        ->editColumn('created_at', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
        })
        ->editColumn('check_in', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          return Carbon::parse($row->check_in)->format('Y-m-d H:i:s');
        })
        ->editColumn('check_out', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          $checkOut = $row->check_out != null ? Carbon::parse($row->check_out)->format('Y-m-d H:i:s') : '';
          return $checkOut;
        })
        // ->editColumn('waktu_shift', function ($row) {
        //   // Menggunakan Carbon untuk memformat created_at
        //   return $row->shift_masuk . ' - ' . $row->shift_pulang;
        // })
        ->editColumn('keterangan', function ($row) {
          $keterangan = $row->keterangan == 'tco' ? 'Tidak Check Out' : $row->keterangan;
          return $keterangan;
        })
        ->addColumn('is_fast', function ($row) {
          if (!$row->shift_pulang || !$row->check_out) {
            return null;
          }

          // Buat object waktu
          // shift_pulang hanya jam -> pakai tanggal dari check_out agar konsisten
          $checkOut = Carbon::parse($row->check_out);
          $shiftPulang = Carbon::parse($row->shift_pulang);

          // Jika jam checkout < jam shift pulang
          if ($checkOut->lt($shiftPulang)) {
            // Hitung selisih dalam jam
            $diffInHours = $shiftPulang->diffInHours($checkOut, false);

            // Kalau selisih besar (misalnya > 6 jam), berarti checkout lewat tengah malam
            if (abs($diffInHours) > 6) {
              $checkOut->addDay(); // anggap checkout di hari berikutnya
            }
          }

          // Return true kalau checkout lebih cepat dari shift_pulang
          return $checkOut->lt($shiftPulang);
        })
        // 🔥 Tambahkan kolom is_late untuk flag pewarnaan (tidak ditampilkan di tabel)
        ->addColumn('is_late', function ($row) {
          $shiftMasuk = Carbon::parse($row->shift_masuk);
          $checkIn = $row->check_in ? Carbon::parse($row->check_in) : null;
          // Jika keduanya ada, bandingkan waktu saja (abaikan tanggal)
          if ($checkIn && $shiftMasuk) {
            // Ekstrak waktu dari check_in dan bandingkan
            // $checkInTime = $checkIn->format('H:i:s');
            // $shiftMasukTime = $shiftMasuk->format('H:i:s');
            return $checkIn > $shiftMasuk ? true : false;
          }
          return false; // Jika salah satu null, tidak kuning
        })
        // 🔥 Tambahkan filter custom di sini
        ->filter(function ($query) use ($request) {
          if ($request->search['value']) {
            $search = strtolower($request->search['value']);
            $query->where(function ($q) use ($search) {
              $q->whereRaw('LOWER(absens.shift) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(absens.status) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(absens.keterangan) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(pegawais.nama_pegawai) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(pegawais.departemen) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(absens.created_at) LIKE ?', ["%{$search}%"]);
            });
          }
        })
        ->rawColumns(['nama_pegawai'])
        ->make(true);
    }
    return view('history-absen.index');
  }

  public function viewShift(Request $request)
  {
    //
    $user = Auth::user();
    $role = $user->pegawai->jabatan;
    $departemen = $user->pegawai->departemen;

    if ($request->ajax()) {
      // ⭐️ 1. Lakukan JOIN ke tabel 'pegawais' dan pilih kolom yang diperlukan.
      $shiftKerja = ShiftKerja::query()
        ->select('shift_kerjas.*', 'pegawais.nama_pegawai', 'pegawais.departemen') // Pilih semua kolom dari shift_kerjas
        ->leftJoin('pegawais', 'shift_kerjas.no_pegawai', '=', 'pegawais.no_pegawai')

        // ⭐️ 2. Lakukan pengurutan berdasarkan kolom relasi
        ->orderBy('pegawais.nama_pegawai', 'ASC') // Urutkan berdasarkan nama_pegawai A-Z
        // Jika Anda ingin pengurutan created_at sebagai urutan sekunder:
        // ->orderBy('shift_kerjas.created_at', 'DESC');
        ->with('pegawai'); // Pertahankan Eager Loading untuk kolom data

      // 🔥 Logika Filter Berdasarkan Jabatan dan Departemen
      if ((strtolower($role) != 'hr') && (strtolower($role) != 'hotel manager')) {
        if ((strtolower($role) == 'supervisor') || (strtolower($role) == 'dept head b')) {
          // Jika Supervisor → tampilkan semua dalam departemen yang sama
          $shiftKerja->where('pegawais.departemen', $departemen);
        } else {
          // Selain itu → tampilkan yang jabatan sama
          $shiftKerja->where('pegawais.no_pegawai', $user->pegawai->no_pegawai);
        }
      }

      return DataTables::of($shiftKerja)
        ->addIndexColumn()
        ->editColumn('waktu_masuk', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          return Carbon::parse($row->waktu_masuk)->format('H:i');
        })
        ->editColumn('waktu_pulang', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          return Carbon::parse($row->waktu_pulang)->format('H:i');
        })
        ->addColumn('nama_pegawai', function ($row) {
          return $row->pegawai ? $row->pegawai->nama_pegawai : 'N/A';
        })
        // ⭐️ Tambahkan ini untuk membuat kolom penomoran otomatis
        ->addColumn('action', function ($row) use ($role) {
          $editBtn = '<a href="/pages/shift-kerja/' . $row->id . '/edit" class="btn btn-sm btn-primary">Edit</a>';
          $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';

          if (strtolower($role) == 'hr' || strtolower($role) == 'supervisor' || strtolower($role) == 'hotel manager' || strtolower($role) == 'dept head b') {
            return $editBtn . ' ' . $deleteBtn;
          }
        })
        ->rawColumns(['action'])

        // 🔥 Tambahkan filter custom di sini
        ->filter(function ($query) use ($request) {
          if ($request->search['value']) {
            $search = strtolower($request->search['value']);
            $query->where(function ($q) use ($search) {
              $q->whereRaw('LOWER(shift_kerjas.shift) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(shift_kerjas.waktu_masuk) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(shift_kerjas.waktu_pulang) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(pegawais.nama_pegawai) LIKE ?', ["%{$search}%"]);
            });
          }
        })
        ->make(true);
    }
    return view('shift-kerja.index');
  }

  public function viewPegawai(Request $request)
  {
    //
    $user = Auth::user();
    $role = $user->pegawai->jabatan;
    $departemen = $user->pegawai->departemen;

    if ($request->ajax()) {
      $pegawai = Pegawai::query()
        ->orderBy('nama_pegawai', 'ASC');

      // 🔥 Logika Filter Berdasarkan Jabatan dan Departemen
      if ((strtolower($role) != 'hr') && (strtolower($role) != 'hotel manager')) {
        if ((strtolower($role) == 'supervisor') || (strtolower($role) == 'dept head b')) {
          // Jika Supervisor → tampilkan semua dalam departemen yang sama
          $pegawai->where('pegawais.departemen', $departemen);
        } else {
          // Selain itu → tampilkan yang jabatan sama
          $pegawai->where('pegawais.no_pegawai', $user->pegawai->no_pegawai);
        }
      }

      return DataTables::of($pegawai)
        // ⭐️ Tambahkan ini untuk membuat kolom penomoran otomatis
        ->addIndexColumn()
        // Edit Column Nama Pegawai
        ->editColumn('nama_pegawai', function ($row) {
          return '<a href="#" data-bs-toggle="modal" data-id="' . $row->id . '" data-bs-target="#detailModal" class="btnDetail">'
            . e($row->nama_pegawai) .
            '</a>';
        })
        // ⭐️ Tambahkan ini untuk membuat kolom penomoran otomatis
        ->addColumn('action', function ($row) use ($role) {
          $editBtn = '<a href="/pages/pegawai/' . $row->id . '/edit" class="btn btn-sm btn-primary">Edit</a>';
          $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
          if (strtolower($role) == 'hr' || strtolower($role) == 'hotel manager') {
            return $editBtn . ' ' . $deleteBtn;
          }
        })
        ->rawColumns(['nama_pegawai', 'action'])
        ->make(true);
    }
    return view('pegawai.index');
  }

  public function viewParamShiftKerja()
  {
    try {
      $param = Param::where('value', 'shift')->first();

      if (!$param) {
        return redirect()->route('config-shift-kerja')->with('error', 'Data parameter shift kerja tidak ditemukan.');
      }

      $value = json_decode($param->svalue);
      return DataTables::of(collect($value))
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
          // Gunakan nilai 'val' sebagai pengganti id
          $editBtn = '<a href="/config/shift-kerja/' . $row->val . '/edit" class="btn btn-sm btn-primary">Edit</a>';
          $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->val . '">Delete</button>';
          return $editBtn . ' ' . $deleteBtn;
        })
        ->editColumn('val', function ($row) {
          $val = strtoupper($row->val);
          return $val;
        })
        ->rawColumns(['action'])
        ->make(true);
    } catch (\Exception $e) {
      return redirect()->route('config-shift-kerja')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }
}
