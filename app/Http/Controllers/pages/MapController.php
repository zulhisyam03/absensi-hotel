<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Param;

class MapController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
    return view("content.config.map");
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
    try {
      $param = Param::where('value', $id)->first();
      $data = json_decode($param->svalue, true);

      if (!$data) {
        return redirect()->route('config-lokasi')->with('error', 'Data tidak ditemukan.');
      }
      return response()->json($data);
      // return view('content.config.map', ['data' => $data, 'flag'
    } catch (\Exception $e) {
      return redirect()->route('config-lokasi')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
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
    $request->validate([
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',
      'radius' => 'required|integer|min:1',
    ]);
    try {
      $param = Param::where('value', $id)->first();

      if (!$param) {
        return redirect()->route('config-lokasi')->with('error', 'Data parameter lokasi tidak ditemukan.');
      }

      $newData = [
        'latitude' => $request->input('latitude'),
        'longitude' => $request->input('longitude'),
        'radius' => $request->input('radius'),
      ];

      $param->svalue = json_encode($newData);
      $param->save();

      return redirect()->route('config-lokasi')->with('success', 'Data lokasi berhasil diperbarui.');
    } catch (\Exception $e) {
      return redirect()->route('config-lokasi')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}