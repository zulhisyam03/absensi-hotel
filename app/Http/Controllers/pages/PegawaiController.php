<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
      'no_handphone' => 'nullable|string|max:20', // sesuai form name
      'alamat' => 'nullable|string|max:500',
      'tempat_lahir' => 'nullable|string|max:100',
      'tanggal_lahir' => 'required|date', // sesuai form name
      'tanggal_join' => 'required|date',  // sesuai form name
      'jenis_kelamin' => 'required|string|max:10'
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
      $pegawai->save();

      // Save To User Table
      $user = new User();
      $user->email = $validated['email'];
      $user->email_verified_at = Carbon::now();
      $user->password = Hash::make($validated['no_pegawai']);
      $user->remember_token = str::random(10);
      $user->save();

      return redirect()->route(route: 'pages-pegawai')->with('success', 'Data pegawai berhasil ditambahkan.');
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
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }

  public function searchPegawai(Request $request)
  {
    $query = $request->get('q', ''); // Ambil keyword dari query param 'q'
    if (empty($query) || strlen($query) < 2) { // Minimal 2 char untuk hindari spam request
      return response()->json([]); // Return array kosong
    }

    // Query DB: Partial match pada nama_pegawai, filter status 'aktif'
    $pegawais = Pegawai::select('nama_pegawai', 'no_pegawai')
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
}