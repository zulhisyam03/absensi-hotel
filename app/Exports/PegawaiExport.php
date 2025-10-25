<?php

namespace App\Exports;

use App\Models\Pegawai;
// use App\Exports\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class PegawaiExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
  protected $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function query()
  {
    $departemen = $this->request->filterDepartemen;

    return Pegawai::query()
      ->select('pegawais.*')
      ->when($departemen, fn($q) => $q->where('pegawais.departemen', $departemen))
      ->orderBy('nama_pegawai', 'asc')
    ;
  }

  public function headings(): array
  {
    return [
      'NO PEGAWAI',
      'NAMA PEGAWAI',
      'NIK',
      'NPWP',
      'BPJS',
      'DEPARTEMEN',
      'NO HANDPHONE',
      'EMERGENCY NUMBER',
      'TANGGAL JOIN',
      'ALAMAT',
      'JABATAN',
      'STATUS KARYAWAN',
      'LAST SALARY', // Sesuaikan dengan kolom yang ada di tabel Anda
    ];
  }

  public function map($attendance): array
  {
    return [
      $attendance->no_pegawai,
      $attendance->nama_pegawai,
      $attendance->nik,
      $attendance->npwp,
      $attendance->bpjs,
      $attendance->departemen,
      $attendance->no_hp,
      $attendance->emergency_number,
      $attendance->tgl_join,
      $attendance->alamat,
      $attendance->jabatan,
      $attendance->status_pegawai,
      (float) $attendance->last_salary,
    ];
  }

  public function columnFormats(): array
  {
    return [
      'C' => NumberFormat::FORMAT_NUMBER,
      'D' => NumberFormat::FORMAT_TEXT,
      'E' => NumberFormat::FORMAT_TEXT,
      'G' => NumberFormat::FORMAT_TEXT,
      'H' => NumberFormat::FORMAT_TEXT,
      'M' => '"Rp."#,##0',
    ];
  }
}
