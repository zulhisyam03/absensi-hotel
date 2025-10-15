<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftKerja;
use App\Models\Param;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

  public function createParameter()
  {
    return view('content.config.shift-form', ['flag' => 'Tambah']);
  }

  public function storeParameter(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'shift' => 'required|string|max:50',
      'waktu_masuk' => 'required|date_format:H:i',
      'waktu_pulang' => 'required|date_format:H:i|after:waktu_masuk'
    ]);

    $validated = $validator->validated();
    $shiftUpper = strtoupper($validated['shift']);

    try {
      // Ambil data param shift
      $param = Param::where('value', 'shift')->firstOrFail();
      $shifts = json_decode($param->svalue, associative: true);

      // Cek apakah shift sudah ada
      $existingShift = collect($shifts)->firstWhere('val', $shiftUpper);
      if ($existingShift) {
        return redirect()->back()->withInput()->with('error', 'Shift ' . $shiftUpper . ' sudah ada.');
      }

      // Tambah data baru
      $newShift = [
        'val' => $shiftUpper,
        'waktu_masuk' => $validated['waktu_masuk'],
        'waktu_pulang' => $validated['waktu_pulang'],
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
      $shifts[] = $newShift;

      // Simpan kembali ke database
      $param->svalue = json_encode($shifts);
      $param->save();

      return redirect()->route('config-shift-kerja')->with('success', 'Parameter shift kerja berhasil ditambahkan.');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  public function updateParameter(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'shift' => 'required|string|max:50',
      'waktu_masuk' => 'required|date_format:H:i',
      'waktu_pulang' => 'required|date_format:H:i|after:waktu_masuk'
    ]);

    $validated = $validator->validated();

    try {
      // Ambil data param shift
      $param = Param::where('value', 'shift')->firstOrFail();
      $shifts = json_decode($param->svalue, associative: true);

      // Cari index item dengan val = $id
      $index = collect($shifts)->search(fn($item) => $item['val'] === $id);
      if ($index === false) {
        return redirect()->back()->withInput()->with('error', 'Shift ' . $id . ' tidak ditemukan.');
      }

      // Update data di index tersebut
      $shifts[$index]['waktu_masuk'] = $validated['waktu_masuk'];
      $shifts[$index]['waktu_pulang'] = $validated['waktu_pulang'];
      $shifts[$index]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

      // Simpan kembali ke database
      $param->svalue = json_encode($shifts);
      $param->save();

      return redirect()->route('config-shift-kerja')->with('success', 'Parameter shift kerja berhasil diupdate.');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  public function deleteParameter($id)
  {
    try {
      // Ambil data param shift
      $param = Param::where('value', 'shift')->firstOrFail();
      $shifts = json_decode($param->svalue, associative: true);

      // Filter keluar item dengan val = $id
      $filteredShifts = array_filter($shifts, fn($item) => $item['val'] !== $id);
      if (count($shifts) === count($filteredShifts)) {
        return redirect()->back()->with('error', 'Shift ' . $id . ' tidak ditemukan.');
      }

      // Simpan kembali ke database
      $param->svalue = json_encode(array_values($filteredShifts)); // reindex array
      $param->save();

      return redirect()->route('config-shift-kerja')->with('success', 'Parameter shift kerja berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }
}