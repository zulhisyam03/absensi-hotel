<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftKerja extends Model
{
  use HasFactory;

  public function pegawai()
  {
    return $this->belongsTo(Pegawai::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: belongsTo(Model, foreign_key_di_ShiftKerja, owner_key_di_Pegawai)
  }
}
