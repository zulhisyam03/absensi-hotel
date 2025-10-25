<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AbsenController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
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
    try {
      $user = Auth::user();
      $noPegawai = $user->pegawai->no_pegawai;
      $checkIn = Carbon::now();
      $shift = $request->shiftAktif;
      $str = $request->waktuShiftAktif;
      $keterangan = '';

      // Hapus karakter yang tidak perlu
      $clean = trim($str, '() ');
      // Pisahkan berdasarkan tanda '-'
      list($waktu_masuk, $waktu_pulang) = array_map('trim', explode('-', $clean));
      $waktu_masuk = Carbon::parse($waktu_masuk)->format('H:i:s');
      $waktu_pulang = Carbon::parse($waktu_pulang)->format('H:i:s');

      // Validasi manual untuk return JSON jika gagal
      $validator = Validator::make($request->all(), [
        'shiftAktif' => 'required',
        'waktuShiftAktif' => 'required', // Tambahkan jika diperlukan
        'foto_absensi' => 'required|image|max:2048',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validasi gagal.',
          'errors' => $validator->errors()
        ], 422);
      }

      // Cek Absen
      $cekAbsen = Absen::where('no_pegawai', $noPegawai)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->where('keterangan', '!=', 'tco')
        ->latest()
        ->first();

      // Jika Ada, maka update Data tersebut untuk Check Out
      if ($cekAbsen) {
        $checkOut = Carbon::now();

        // Simpan gambar untuk check-out
        $path = null;
        if ($request->hasFile('foto_absensi')) {
          $file = $request->file('foto_absensi');
          $filename = 'checkout_' . $noPegawai . '_' . time() . '.' . $file->getClientOriginalExtension();
          $path = $file->storeAs('check-out', $filename, 'public');
        }

        $cepatPulang = $this->isCepatPulang($checkOut, $waktu_masuk, $waktu_pulang);
        if ($cepatPulang) {
          $keterangan = 'cepat pulang';
        }

        // Update record absen
        $cekAbsen->update([
          'check_out' => $checkOut,
          'pict_out' => $path,
          'status' => 'check out',
          'keterangan' => $keterangan
        ]);

        Log::channel('shift')->info('Absen Check Out:', $cekAbsen->toArray()); // Untuk melihat semua atribut

        return response()->json([
          'success' => true,
          'message' => 'Check-out berhasil disimpan.',
          'data' => $cekAbsen
        ]);

      } else {
        // Jika Tidak ada data, maka akan buat record baru untuk absen Check In
        // Simpan gambar untuk check-in
        $path = null;
        if ($request->hasFile('foto_absensi')) {
          $file = $request->file('foto_absensi');
          $filename = 'checkin_' . $noPegawai . '_' . time() . '.' . $file->getClientOriginalExtension();
          $path = $file->storeAs('check-in', $filename, 'public');
        }

        $telat = $this->isTerlambat($checkIn, $waktu_masuk, $waktu_pulang);
        if ($telat) {
          $keterangan = 'telat';
        }

        // Buat record baru
        $absen = Absen::create([
          'no_pegawai' => $noPegawai,
          'check_in' => $checkIn,
          'pict_in' => $path,
          'shift' => $shift,
          'shift_masuk' => $waktu_masuk,
          'shift_pulang' => $waktu_pulang,
          'status' => 'check in',
          'keterangan' => $keterangan
        ]);
        Log::channel('shift')->info('Absen Check In:', $absen->toArray()); // Untuk melihat semua atribut

        return response()->json([
          'success' => true,
          'message' => 'Check-in berhasil disimpan.',
          'data' => $absen
        ]);
      }

    } catch (\Exception $e) {
      // Tangkap semua exception dan return JSON error
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
      ], 500);
    }
  }


  /**
   * Display the specified resource.
   */
  public function show(Absen $absen)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Absen $absen)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Absen $absen)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Absen $absen)
  {
    //
  }

  public function export(Request $request)
  {
    // Validasi input jika diperlukan
    $request->validate([
      'filterDateStart' => 'nullable|date',
      'filterDateEnd' => 'nullable|date',
      'filterDepartemen' => 'nullable|string',
    ]);
    // Export dengan nama file dinamis
    $filename = 'absen_history' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    return Excel::download(new AttendanceExport($request), $filename);
  }

  private function isTerlambat($checkIn, $waktuMasuk, $waktuPulang)
  {
    $checkIn = Carbon::parse($checkIn);
    $tanggal = $checkIn->copy()->startOfDay();

    $masuk = $tanggal->copy()->setTimeFromTimeString($waktuMasuk);
    $pulang = $tanggal->copy()->setTimeFromTimeString($waktuPulang);

    // Jika waktu pulang lebih kecil dari masuk → shift lintas hari
    if ($pulang->lt($masuk)) {
      $pulang->addDay(); // pulang besok
    }

    // Jika shift malam (lintas hari) dan checkin sebelum jam masuk (misal 00:30 tapi shift 22:00-05:00)
    if ($checkIn->lt($masuk) && $checkIn->isSameDay($pulang)) {
      // artinya dia absen di hari berikutnya tapi shift sudah mulai
      $masuk->subDay(); // ubah jam masuk ke hari sebelumnya
    }

    // Bandingkan
    return $checkIn->gt($masuk); // true = telat, false = tepat waktu
  }

  private function isCepatPulang($checkOut, $waktuMasuk, $waktuPulang)
  {
    $checkOut = Carbon::parse($checkOut);
    $tanggal = $checkOut->copy()->startOfDay();

    $masuk = $tanggal->copy()->setTimeFromTimeString($waktuMasuk);
    $pulang = $tanggal->copy()->setTimeFromTimeString($waktuPulang);

    // 🔹 Jika shift lintas hari (contoh 20:00 - 02:00)
    if ($pulang->lt($masuk)) {
      $pulang->addDay();
    }

    // 🔹 Jika checkout sebelum jam masuk tapi masih di hari pulang (lintas hari)
    if ($checkOut->lt($masuk) && $checkOut->isSameDay($pulang)) {
      $checkOut->addDay();
    }

    // 🔹 Cek apakah checkout lebih awal dari waktu pulang
    return $checkOut->lt($pulang);
  }
}
