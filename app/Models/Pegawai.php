<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
  use HasFactory;

  protected $fillable = [
    'no_pegawai',
    'nama_pegawai',
    'nik',
    'no_kk',
    'npwp',
    'bpjs',
    'bpjs_tk',
    'departemen',
    'jabatan',
    'alamat',
    'tanggal_join',
    'status_karyawan',
    'foto',
    'foto_version', // 🔹 Tambahkan ini
  ];

  public function absens()
  {
    return $this->hasMany(Absen::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
  public function shift()
  {
    return $this->hasMany(ShiftKerja::class, 'no_pegawai', 'no_pegawai');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
  public function user()
  {
    return $this->hasOne(User::class, 'email', 'email');
    // Syntaks: hasMany(Model, foreign_key_di_ShiftKerja, local_key_di_Pegawai)
  }
}
