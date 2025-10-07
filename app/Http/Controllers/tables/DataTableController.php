<?php

namespace App\Http\Controllers\tables;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\User;

class DataTableController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    //
    if ($request->ajax()) {
      return DataTables::of(User::query())
        ->addColumn('action', function ($row) {
          $editBtn = '<a href="/users/' . $row->id . '/edit" class="btn btn-sm btn-primary">Edit</a>';
          $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
          return $editBtn . ' ' . $deleteBtn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('users.index');
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
}