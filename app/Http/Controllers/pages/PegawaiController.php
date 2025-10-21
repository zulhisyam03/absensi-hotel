<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Exports\PegawaiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
    return view('content.pages.pages-pegawai');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
    return view('content.form.pages-pegawai-form', ['flag' => 'Tambah']);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
    // validasi: gunakan nama input yang sama dengan form
    $validated = $request->validate([
      'no_pegawai' => 'required|string|max:50',
      'nama_pegawai' => 'required|string|max:255',
      'jabatan' => 'required|string|max:30',
      'email' => 'required|email|max:255',
      'no_handphone' => 'required|string|max:20', // sesuai form name
      'alamat' => 'required|string|max:500',
      'tempat_lahir' => 'required|string|max:100',
      'tanggal_lahir' => 'required|date', // sesuai form name
      'tanggal_join' => 'required|date',  // sesuai form name
      'jenis_kelamin' => 'required|string|max:10',
      'departemen' => 'required|string|max:50', // sesuai form name
      'status_pegawai' => 'required|string|max:20',
      'emergency_number' => 'required|string|max:14',
      'nik' => 'required|string|max:20',
      'npwp' => 'required|string|max:20',
      'bpjs' => 'required|string|max:20',
      'last_salary' => 'required|integer|between:1,9999999999'
    ]);
    try {

      // Save To pegawais Table
      $pegawai = new Pegawai();
      $pegawai->no_pegawai = strtoupper($validated['no_pegawai']);
      $pegawai->nama_pegawai = $validated['nama_pegawai'];
      $pegawai->jabatan = $validated['jabatan'];
      $pegawai->email = $validated['email'];
      $pegawai->no_hp = $validated['no_handphone'] ?? null; // map ke kolom DB
      $pegawai->alamat = $validated['alamat'] ?? null;
      $pegawai->tempat_lahir = $validated['tempat_lahir'] ?? null;
      $pegawai->tgl_lahir = $validated['tanggal_lahir'];
      $pegawai->tgl_join = $validated['tanggal_join'];
      $pegawai->jenis_kelamin = $validated['jenis_kelamin'];
      $pegawai->status = 'aktif';
      $pegawai->departemen = $validated['departemen'];
      $pegawai->status_pegawai = $validated['status_pegawai'];
      $pegawai->emergency_number = $validated['emergency_number'];
      $pegawai->nik = $validated['nik'];
      $pegawai->npwp = $validated['npwp'];
      $pegawai->bpjs = $validated['bpjs'];
      $pegawai->last_salary = $validated['last_salary'];
      $pegawai->save();

      // Save To User Table
      $user = new User();
      $user->email = $validated['email'];
      $user->email_verified_at = Carbon::now();
      $user->password = Hash::make(strtolower($validated['no_pegawai']));
      $user->remember_token = Str::random(10);
      $user->save();

      return redirect()->route('pages-pegawai')->with('success', 'Data pegawai berhasil ditambahkan.');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
    $data = Pegawai::where('id', $id)
      ->where('status', 'aktif')
      ->firstOrFail();

    try {
      if (!$data) {
        return redirect()->route('pages-pegawai')->with('error', 'Data pegawai tidak ditemukan.');
      } else {
        $data['jenis_kelamin'] = $data->jenis_kelamin === 'L' ? 'Laki-laki' : ($data->jenis_kelamin === 'P' ? 'Perempuan' : 'Tidak Diketahui');
        return response()->json($data);
      }
    } catch (\Exception $e) {
      return redirect()->route('pages-pegawai')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
    $data = Pegawai::where('id', $id)
      ->where('status', 'aktif')
      ->firstOrFail();
    try {
      if (!$data) {
        return redirect()->route('pages-pegawai')->with('error', 'Data pegawai tidak ditemukan.');
      } else {
        return view('content.form.pages-pegawai-form', ['flag' => 'Edit', 'data' => $data]);
      }
    } catch (\Exception $e) {
      return redirect()->route('pages-pegawai')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $validator = Validator::make($request->all(), [
      'no_pegawai' => 'required|string|max:50',
      'nama_pegawai' => 'required|string|max:255',
      'jabatan' => 'required|string|max:30',
      'email' => 'required|email|max:255',
      'no_handphone' => 'required|string|max:20', // sesuai form name
      'alamat' => 'required|string|max:500',
      'tempat_lahir' => 'required|string|max:100',
      'tanggal_lahir' => 'required|date', // sesuai form name
      'tanggal_join' => 'required|date',  // sesuai form name
      'jenis_kelamin' => 'required|string|max:10',
      'departemen' => 'required|string|max:50', // sesuai form name
      'status_pegawai' => 'required|string|max:20',
      'emergency_number' => 'required|string|max:14',
      'nik' => 'required|string|max:20',
      'npwp' => 'required|string|max:20',
      'bpjs' => 'required|string|max:20',
      'last_salary' => 'required|integer|between:1,9999999999'
    ]);
    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $validated = $validator->validated();

    try {
      $pegawai = Pegawai::find($id);
      if (!$pegawai) {
        return redirect()->route('pages-pegawai')->with('error', 'Data pegawai tidak ditemukan.');
      }

      // Update fields
      $pegawai->no_pegawai = strtoupper($validated['no_pegawai']);
      $pegawai->nama_pegawai = $validated['nama_pegawai'];
      $pegawai->jabatan = $validated['jabatan'];
      $pegawai->email = $validated['email'];
      $pegawai->no_hp = $validated['no_handphone']; // map ke kolom DB
      $pegawai->alamat = $validated['alamat'];
      $pegawai->tempat_lahir = $validated['tempat_lahir'];
      $pegawai->tgl_lahir = $validated['tanggal_lahir'];
      $pegawai->tgl_join = $validated['tanggal_join'];
      $pegawai->jenis_kelamin = $validated['jenis_kelamin'];
      $pegawai->departemen = $validated['departemen'];
      $pegawai->status_pegawai = $validated['status_pegawai'];
      $pegawai->emergency_number = $validated['emergency_number'];
      $pegawai->nik = $validated['nik'];
      $pegawai->npwp = $validated['npwp'];
      $pegawai->bpjs = $validated['bpjs'];
      $pegawai->last_salary = $validated['last_salary'];
      // Simpan perubahan
      $pegawai->save();

      // Update User Table jika email berubah
      $user = User::where('email', $pegawai->email)->first();
      if ($user) {
        $user->email = $validated['email'];
        $user->save();
      }

      return redirect()->route('pages-pegawai')->with('success', 'Data pegawai berhasil diperbarui.');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      \DB::beginTransaction();

      // Cari pegawai, throws ModelNotFoundException jika tidak ada
      $pegawai = Pegawai::findOrFail($id);

      // Hapus pegawai
      $pegawai->delete();

      \DB::commit();

      return redirect()->route('pages-pegawai')->with('success', 'Data pegawai berhasil dihapus.');
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      \DB::rollBack();
      return redirect()->route('pages-pegawai')->with('error', 'Data pegawai tidak ditemukan.');
    } catch (\Exception $e) {
      \DB::rollBack();
      return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  public function searchPegawai(Request $request)
  {
    $query = $request->get('q', ''); // Ambil keyword dari query param 'q'
    if (empty($query) || strlen($query) < 2) { // Minimal 2 char untuk hindari spam request
      return response()->json([]); // Return array kosong
    }

    // Query DB: Partial match pada nama_pegawai, filter status 'aktif'
    $pegawais = Pegawai::select('nama_pegawai', 'no_pegawai', 'departemen')
      ->where('nama_pegawai', 'LIKE', '%' . $query . '%') // Case-insensitive partial match
      ->where('status', 'aktif') // Filter hanya yang aktif (seperti request sebelumnya)
      ->distinct() // Hindari duplikat
      ->limit(10) // Batasi 10 hasil untuk performa
      ->orderBy('nama_pegawai', 'ASC') // Urutkan alfabetis
      ->get();
    // Return JSON response
    return response()->json($pegawais); // Array sederhana: ["Rendy", "Ana", ...]
    // Atau dengan wrapper jika ingin: return response()->json(['data' => $pegawais]);
  }

  public function export(Request $request)
  {
    // Validasi input jika diperlukan
    $request->validate([
      'filterDepartemen' => 'nullable|string',
    ]);
    // Export dengan nama file dinamis
    $filename = 'Daftar_Pegawai_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    return Excel::download(new PegawaiExport($request), $filename);
  }
}
