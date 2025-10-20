<?php

namespace App\Exports;

use App\Models\Absen;
use App\Models\Pegawai;
// use App\Exports\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function query()
  {
    $startDate = $this->request->filterDateStart;
    $endDate = $this->request->filterDateEnd;
    // Jika endDate diisi, tambahkan jam 23:59:59 agar mencakup seluruh hari
    if ($endDate) {
      $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
    }
    $departemen = $this->request->filterDepartemen;

    return Absen::query()
      ->select(
        'absens.*',
        'pegawais.nama_pegawai',
        'pegawais.departemen',
        DB::raw("CONCAT(absens.shift_masuk, ' - ', absens.shift_pulang) as waktu_shift"),
        DB::raw('COUNT(absens.id) OVER (PARTITION BY absens.no_pegawai) as total_hari_kerja')
      )
      ->join('pegawais', 'pegawais.no_pegawai', '=', 'absens.no_pegawai')
      ->when($departemen, fn($q) => $q->where('pegawais.departemen', $departemen))
      ->when($startDate && $endDate, fn($q) => $q->whereBetween('absens.check_in', [$startDate, $endDate]))
      ->orderBy('absens.no_pegawai')
      ->orderBy('absens.check_in');
  }

  public function headings(): array
  {
    return [
      'NO PEGAWAI',
      'NAMA PEGAWAI',
      'DEPARTEMEN',
      'SHIFT',
      'WAKTU SHIFT',
      'CHECK IN',
      'CHECK OUT', // Sesuaikan dengan kolom yang ada di tabel Anda
      'TOTAL HARI KERJA', // Sesuaikan dengan kolom yang ada di tabel Anda
    ];
  }

  public function map($attendance): array
  {
    return [
      $attendance->no_pegawai,
      $attendance->nama_pegawai,
      $attendance->departemen,
      $attendance->shift,
      $attendance->waktu_shift,
      $attendance->check_in,
      $attendance->check_out,
      $attendance->total_hari_kerja,
    ];
  }
}
