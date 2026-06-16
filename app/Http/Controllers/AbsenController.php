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

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Atau gunakan \Logisk\Drivers\Imagick\Driver jika pakai Imagick

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
      list($waktu_masuk, $waktu_pulang) = array_map('trim', explode('-', $clean));
      $waktu_masuk = Carbon::parse($waktu_masuk)->format('H:i:s');
      $waktu_pulang = Carbon::parse($waktu_pulang)->format('H:i:s');

      // ✅ Tentukan tanggal lengkap untuk shift lintas hari
      $tanggalShift = $checkIn->copy()->startOfDay();
      // Jika shift pulang lebih kecil dari masuk, berarti lintas hari (tetap seperti asli, tapi opsional jika ingin digabungkan)
      if (Carbon::parse($waktu_masuk)->gt(Carbon::parse($waktu_pulang))) {
        if ($checkIn->format('H:i:s') < $waktu_pulang) {
          // Check-in lewat tengah malam, berarti shift dimulai kemarin
          $tanggalShift->subDay();
        }
      }
      // Kondisi baru: Jika shift_masuk <= 03:00 dan check_in antara 21:00 - 23:59:59, addDay
      $checkInTime = $checkIn->format('H:i:s');
      if ($waktu_masuk <= '03:00:00' && $checkInTime >= '21:00:00' && $checkInTime <= '23:59:59') {
        $tanggalShift->addDay();
      }
      // Bentuk waktu shift lengkap
      $shiftStart = $tanggalShift->copy()->setTimeFromTimeString($waktu_masuk);
      $shiftEnd = $shiftStart->copy();
      // Perbaikan: Tambahkan kondisi untuk shift yang dimulai pukul 00:00 atau lintas hari (seperti sebelumnya, jika masih diperlukan)
      if (Carbon::parse($waktu_masuk)->gt(Carbon::parse($waktu_pulang)) || $waktu_masuk == '00:00:00') {
        $shiftEnd->addDay()->setTimeFromTimeString($waktu_pulang);
      } else {
        $shiftEnd->setTimeFromTimeString($waktu_pulang);
      }

      // Validasi manual untuk return JSON jika gagal
      $validator = Validator::make($request->all(), [
        'shiftAktif' => 'required',
        'waktuShiftAktif' => 'required',
        'foto_absensi' => 'required|image|max:2048',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validasi gagal.',
          'errors' => $validator->errors()
        ], 422);
      }

      // Cek Absen aktif (belum check-out)
      $cekAbsen = Absen::where('no_pegawai', $noPegawai)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->where('keterangan', '!=', 'tco')
        ->latest()
        ->first();

      // ======================================================
      // CHECK OUT
      // ======================================================
      if ($cekAbsen) {
        $checkOut = Carbon::now();
        $path = null;

        if ($request->hasFile('foto_absensi')) {
          $file = $request->file('foto_absensi');
          $filename = 'checkout_' . $noPegawai . '_' . time() . '.' . $file->getClientOriginalExtension();

          // 1. Buat instance ImageManager dengan driver GD
          $manager = new ImageManager(new Driver());

          // 2. Baca gambar menggunakan manager tersebut
          $image = $manager->read($file)->scale(width: 800);
          $encoded = $image->toJpeg(75);

          // 3. Simpan ke storage
          $path = 'check-out/' . $filename;
          Storage::disk('public')->put($path, $encoded);
        }

        // Ambil waktu shift pulang dari record absen
        $shiftEnd = Carbon::parse($cekAbsen->shift_pulang);
        $keterangan = $cekAbsen->keterangan;

        $cepatPulang = $this->isCepatPulang($checkOut, $shiftStart, $shiftEnd);
        if ($cepatPulang) {
          $keterangan = 'cepat pulang';
        }

        $cekAbsen->update([
          'check_out' => $checkOut,
          'pict_out' => $path,
          'status' => 'check out',
          'keterangan' => $keterangan
        ]);

        Log::channel('shift')->info('Absen Check Out:', $cekAbsen->toArray());

        return response()->json([
          'success' => true,
          'message' => 'Check-out berhasil disimpan.',
          'data' => $cekAbsen
        ]);
      }

      // ======================================================
      // CHECK IN
      // ======================================================
      else {
        $path = null;

        if ($request->hasFile('foto_absensi')) {
          $file = $request->file('foto_absensi');
          $filename = 'checkin_' . $noPegawai . '_' . time() . '.' . $file->getClientOriginalExtension();

          // 1. Buat instance ImageManager dengan driver GD
          $manager = new ImageManager(new Driver());

          // 2. Baca gambar menggunakan manager tersebut
          $image = $manager->read($file)->scale(width: 800);
          $encoded = $image->toJpeg(75);

          // 3. Simpan ke storage
          $path = 'check-in/' . $filename;
          Storage::disk('public')->put($path, $encoded);
        }

        $telat = $this->isTerlambat($checkIn, $shiftStart, $shiftEnd);
        if ($telat) {
          $keterangan = 'telat';
        }

        $absen = Absen::create([
          'no_pegawai' => $noPegawai,
          'check_in' => $checkIn,
          'pict_in' => $path,
          'shift' => $shift,
          'shift_masuk' => $shiftStart,  // Sudah pakai tanggal lengkap
          'shift_pulang' => $shiftEnd,   // Sudah pakai tanggal lengkap
          'status' => 'check in',
          'keterangan' => $keterangan
        ]);

        Log::channel('shift')->info('Absen Check In:', $absen->toArray());

        return response()->json([
          'success' => true,
          'message' => 'Check-in berhasil disimpan.',
          'data' => $absen
        ]);
      }

    } catch (\Exception $e) {
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

  private function isTerlambat($checkIn, $shiftStart, $shiftEnd)
  {
    try {
      $checkIn = Carbon::parse($checkIn);
      $shiftStart = Carbon::parse($shiftStart);
      $shiftEnd = Carbon::parse($shiftEnd);

      Log::channel('shift')->info('isTerlambat() - CheckIn: ' . $checkIn);
      Log::channel('shift')->info('isTerlambat() - ShiftStart: ' . $shiftStart);
      Log::channel('shift')->info('isTerlambat() - ShiftEnd: ' . $shiftEnd);

      // Jika check-in lewat dari waktu shift masuk → telat
      return $checkIn->gt($shiftStart);
    } catch (\Exception $e) {
      Log::channel('shift')->error('Error di isTerlambat(): ' . $e->getMessage());
      return false;
    }
  }


  private function isCepatPulang($checkOut, $shiftStart, $shiftEnd)
  {
    try {
      $checkOut = Carbon::parse($checkOut);
      $shiftStart = Carbon::parse($shiftStart);
      $shiftEnd = Carbon::parse($shiftEnd);

      Log::channel('shift')->info('isCepatPulang() - CheckOut: ' . $checkOut);
      Log::channel('shift')->info('isCepatPulang() - ShiftStart: ' . $shiftStart);
      Log::channel('shift')->info('isCepatPulang() - ShiftEnd: ' . $shiftEnd);

      // Jika check-out lebih awal dari jam pulang → cepat pulang
      return $checkOut->lt($shiftEnd);
    } catch (\Exception $e) {
      Log::channel('shift')->error('Error di isCepatPulang(): ' . $e->getMessage());
      return false;
    }
  }

}