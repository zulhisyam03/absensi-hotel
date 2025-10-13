<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;

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
    $pegawais = Pegawai::select('nama_pegawai')
      ->where('nama_pegawai', 'LIKE', '%' . $query . '%') // Case-insensitive partial match
      ->where('status', 'aktif') // Filter hanya yang aktif (seperti request sebelumnya)
      ->distinct() // Hindari duplikat
      ->limit(10) // Batasi 10 hasil untuk performa
      ->orderBy('nama_pegawai', 'ASC') // Urutkan alfabetis
      ->pluck('nama_pegawai') // Ambil hanya array nama_pegawai
      ->toArray();
    // Return JSON response
    return response()->json($pegawais); // Array sederhana: ["Rendy", "Ana", ...]
    // Atau dengan wrapper jika ingin: return response()->json(['data' => $pegawais]);
  }
}
