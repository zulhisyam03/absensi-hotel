<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
  use HasFactory;

  public function absens()
  {
    return $this->hasMany(Absen::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
  public function shift()
  {
    return $this->hasMany(Shiftkerja::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
  public function user()
  {
    return $this->hasOne(User::class, 'email', 'email');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
}
