<?php

namespace App\Exports;

use App\Models\Absen;
use App\Models\Pegawai;
// use App\Exports\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Untuk WithStyles

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
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
      'SHIFT MASUK',
      'SHIFT PULANG',
      'CHECK IN',
      'CHECK OUT', // Sesuaikan dengan kolom yang ada di tabel Anda
      'KETERANGAN', // Sesuaikan dengan kolom yang ada di tabel Anda
      'TOTAL HARI KERJA', // Sesuaikan dengan kolom yang ada di tabel Anda
    ];
  }

  public function map($attendance): array
  {
    // Modifikasi keterangan: jika 'tco', ubah menjadi 'TIDAK CHECKOUT'
    $keterangan = $attendance->keterangan;
    if (strtolower($keterangan) == 'tco') { // Case-insensitive, tapi asumsikan lowercase
      $keterangan = 'TIDAK CHECKOUT';
    }
    return [
      $attendance->no_pegawai,
      strtoupper($attendance->nama_pegawai),
      strtoupper($attendance->departemen),
      strtoupper($attendance->shift),
      $attendance->shift_masuk,
      $attendance->shift_pulang,
      $attendance->check_in,
      $attendance->check_out,
      strtoupper($keterangan),
      $attendance->total_hari_kerja,
    ];
  }
  // Tambahkan method styles() untuk conditional styling per row
  public function styles(Worksheet $sheet)
  {
    $styles = [];
    $rows = $sheet->getHighestRow();
    for ($row = 2; $row <= $rows; $row++) {
      $keterangan = $sheet->getCell('I' . $row)->getValue();
      if (!empty($keterangan)) {
        $styles[$row] = [
          'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFF0000'], // Merah
          ],
        ];
      }
    }
    return $styles;
  }
  // Event AfterSheet tetap ada jika perlu styling tambahan (misalnya, header)
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        // Opsional: Styling header atau lainnya
        $event->sheet->getStyle('A1:I1')->getFont()->setBold(true); // Header bold
      },
    ];
  }
}
