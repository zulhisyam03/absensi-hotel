<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    return view('content.form.pages-pegawai-form');
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
}
