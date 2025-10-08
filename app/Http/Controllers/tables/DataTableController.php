<?php

namespace App\Http\Controllers\tables;

use App\Http\Controllers\Controller;
use App\Models\ShiftKerja;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absen;
use App\Models\Pegawai;
use Carbon\Carbon;

class DataTableController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    //
    if ($request->ajax()) {
      $absensi = Absen::query()
        ->select('absens.*') // Pilih semua kolom dari absens
        ->leftJoin('pegawais', 'absens.no_karyawan', '=', 'pegawais.no_karyawan')

        // ⭐️ 2. Lakukan pengurutan berdasarkan kolom relasi
        ->orderBy('absens.created_at', 'DESC') // Urutkan berdasarkan nama_pegawai A-Z
        // Jika Anda ingin pengurutan created_at sebagai urutan sekunder:
        // ->orderBy('shift_kerjas.created_at', 'DESC');
        ->with('pegawai'); // Pertahankan Eager Loading untuk kolom data

      return DataTables::of($absensi)
        // ⭐️ Tambahkan ini untuk membuat kolom penomoran otomatis
        ->addIndexColumn()
        ->addColumn('nama_pegawai', function ($row) {
          return $row->pegawai ? $row->pegawai->nama_pegawai : 'N/A';
        })
        ->editColumn('created_at', function ($row) {
          // Menggunakan Carbon untuk memformat created_at
          return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
        })
        ->make(true);
    }
    return view('history-absen.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }

  public function viewShift(Request $request)
  {
    //
    if ($request->ajax()) {
      // ⭐️ 1. Lakukan JOIN ke tabel 'pegawais' dan pilih kolom yang diperlukan.
      $shiftKerja = ShiftKerja::query()
        ->select('shift_kerjas.*') // Pilih semua kolom dari shift_kerjas
        ->leftJoin('pegawais', 'shift_kerjas.no_karyawan', '=', 'pegawais.no_karyawan')

        // ⭐️ 2. Lakukan pengurutan berdasarkan kolom relasi
        ->orderBy('pegawais.nama_pegawai', 'ASC') // Urutkan berdasarkan nama_pegawai A-Z
        // Jika Anda ingin pengurutan created_at sebagai urutan sekunder:
        // ->orderBy('shift_kerjas.created_at', 'DESC');
        ->with('pegawai'); // Pertahankan Eager Loading untuk kolom data

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
        ->addColumn('action', function ($row) {
          $editBtn = '<a href="/shift-kerja/' . $row->id . '/edit" class="btn btn-sm btn-primary">Edit</a>';
          $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
          return $editBtn . ' ' . $deleteBtn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('shift-kerja.index');
  }
}
