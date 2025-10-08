<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'nama_pegawai',
    'shift',
    'pict',
    'status',
    'keterangan',
  ];
}
