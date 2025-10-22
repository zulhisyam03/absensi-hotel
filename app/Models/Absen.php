<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
  use HasFactory;

  protected $fillable = [
    'no_pegawai',
    'shift',
    'pict',
    'status',
    'keterangan',
  ];

  protected $casts = [
    'check_in' => 'datetime',
    'check_out' => 'datetime',
  ];


  public function pegawai()
  {
    return $this->belongsTo(Pegawai::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: belongsTo(Model, foreign_key_di_Absen, owner_key_di_Pegawai)
  }
}
