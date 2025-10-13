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
}
