<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
  use HasFactory;

  public function shifts()
  {
    return $this->hasMany(ShiftKerja::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
}
