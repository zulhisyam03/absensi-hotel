<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftKerja;
use App\Models\Param;

class ShiftKerjaController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
    return view('content.pages.pages-shift-kerja');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
    return view('content.form.pages-shift-kerja-form', ['flag' => 'Tambah']);
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
    $data = ShiftKerja::With('pegawai')
      ->where('id', $id)
      ->where('flag', 'a')
      ->firstOrFail();

    try {
      if (!$data) {
        return redirect()->route('pages-shift-kerja')->with('error', 'Data shift kerja tidak ditemukan.');
      } else {
        return view('content.form.pages-shift-kerja-form', ['flag' => 'Edit', 'data' => $data]);
      }
    } catch (\Exception $e) {
      return redirect()->route('pages-shift-kerja')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

  public function viewConfig()
  {
    return view('content.config.shift');
  }

  public function editParameter($request)
  {
    try {
      $param = Param::where('value', 'shift')->firstOrFail();
      // Decode JSON svalue menjadi array PHP
      $shifts = json_decode($param->svalue, true);

      // Cari item dengan val = $request
      $dataShift = collect($shifts)->firstWhere('val', $request);

      // Kalau tidak ketemu
      if (!$dataShift) {
        return response()->json(['error' => 'Shift ' . $request . ' tidak ditemukan'], 404);
      }

      return view('content.config.shift-form', [
        'flag' => 'Edit',
        'data' => (object) $dataShift // ubah array jadi object
      ]);

    } catch (\Exception $e) {
      return redirect()->route('config-shift-kerja')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }
}
