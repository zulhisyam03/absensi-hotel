<?php

use App\Http\Controllers\AbsenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\UserController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\pages\MapController;
use App\Http\Controllers\pages\ShiftKerjaController;
use App\Http\Controllers\pages\PegawaiController;
use App\Http\Controllers\pages\PayrollController;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\tables\DataTableController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Main Page Route
// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/', function () {
  if (Auth::check()) {
    return redirect()->route('dashboard-analytics');
  } else {
    return redirect()->route('login');
  }
});
// pages on middleware
Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');

  // Export Absen History
  Route::post('/attendance/export', [AbsenController::class, 'export'])->middleware(['role:hr,supervisor'])->name('attendance.export');

  // Pegawai
  Route::get('/pages/daftar-pegawai', [PegawaiController::class, 'index'])->middleware(['role:hr,supervisor'])->name('pages-pegawai');
  Route::get('/pages/pegawai/create', [PegawaiController::class, 'create'])->middleware(['role:hr'])->name('pegawai.create');
  Route::post('/pages/pegawai/store', [PegawaiController::class, 'store'])->middleware(['role:hr'])->name('pegawai.store');
  Route::get('/pages/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->middleware(['role:hr'])->name('pegawai.edit');
  Route::put('/pages/pegawai/{id}', [PegawaiController::class, 'update'])->middleware(['role:hr'])->name('pegawai.update');
  Route::delete('/pages/pegawai/delete/{id}', [PegawaiController::class, 'destroy'])->middleware(['role:hr'])->name('pegawai.delete');
  Route::get('/pegawai/{id}/detail', [PegawaiController::class, 'show'])->middleware(['role:hr,supervisor'])->name('pegawai.show');
  // Route baru: Search nama pegawai untuk autocomplete (GET)
  Route::get('/pegawai/search', [PegawaiController::class, 'searchPegawai'])->name('pegawai.search');
  // Export Pegawai
  Route::post('/pegawai/export', [PegawaiController::class, 'export'])->name('pegawai.export');

  // Shift Kerja
  Route::get('/pages/shift-kerja', [ShiftKerjaController::class, 'index'])->middleware(['role:hr,supervisor,staff'])->name('pages-shift-kerja');
  Route::get('/pages/shift-kerja/create', [ShiftKerjaController::class, 'create'])->middleware(['role:hr,supervisor'])->name('pages-shift-kerja.create');
  Route::post('/pages/shift-kerja/store', [ShiftKerjaController::class, 'store'])->middleware(['role:hr,supervisor'])->name('pages-shift-kerja.store');
  Route::get('/pages/shift-kerja/{id}/edit', [ShiftKerjaController::class, 'edit'])->middleware(['role:hr,supervisor'])->name('pages-shift-kerja.edit');
  Route::put('/pages/shift-kerja/{id}', [ShiftKerjaController::class, 'update'])->middleware(['role:hr,supervisor'])->name('pages-shift-kerja.update');
  Route::delete('/pages/shift-kerja/delete/{id}', [ShiftKerjaController::class, 'destroy'])->middleware(['role:hr,supervisor'])->name('pages-shift-kerja.delete');
  Route::get('/config/shift-kerja', [ShiftKerjaController::class, 'viewConfig'])->middleware(['role:hr'])->name('config-shift-kerja');
  Route::post('/config/shift-kerja/store', [ShiftKerjaController::class, 'storeParameter'])->middleware(['role:hr'])->name('config-shift-kerja.store');
  Route::get('/config/shift-kerja/create', [ShiftKerjaController::class, 'createParameter'])->middleware(['role:hr'])->name('config-shift-kerja.create');
  Route::get('/config/shift-kerja/{id}/edit', [ShiftKerjaController::class, 'editParameter'])->middleware(['role:hr'])->middleware(['role:hr'])->name('config-shift-kerja.edit');
  Route::put('/config/shift-kerja/{id}', [ShiftKerjaController::class, 'updateParameter'])->middleware(['role:hr'])->name('config-shift-kerja.update');
  Route::delete('/config/shift-kerja/delete/{id}', [ShiftKerjaController::class, 'deleteParameter'])->middleware(['role:hr'])->name('config-shift-kerja.delete');
  Route::get('/param/shift-kerja/view/{rout}', [ShiftKerjaController::class, 'viewParameter'])->name('param-shift-kerja.view');
  Route::get('/param/shift-kerja/show/{shift}', [ShiftKerjaController::class, 'showParameterByShift'])->name('param-shift-kerja.showByShift');
  // Payroll
  Route::get('/pages/payroll', [PayrollController::class, 'index'])->name('pages-payroll');

  // Lokasi
  Route::get('config/lokasi', [MapController::class, 'index'])->middleware(['role:hr'])->name('config-lokasi');
  Route::get('config/lokasi/{id}', [MapController::class, 'show'])->middleware(['role:hr'])->name('config-lokasi.show');
  Route::put('config/lokasi/updated/{id}', [MapController::class, 'update'])->middleware(['role:hr'])->name('config-lokasi.update');

  // user
  Route::get("config/user", [UserController::class, 'index'])->name('config-user');
  Route::post("config/user/update", [UserController::class, 'update'])->name('config-user.update');

  // DataTables AJAX route
  Route::get('/datatable/history-absen', [DataTableController::class, 'index'])->name('history-absen.index');
  Route::get('/datatable/shift-kerja', [DataTableController::class, 'viewShift'])->name('shift-kerja.index');
  Route::get('/datatable/param-shift-kerja', [DataTableController::class, 'viewParamShiftKerja'])->name('param-shift-kerja.index');
  Route::get('/datatable/pegawai', [DataTableController::class, 'viewPegawai'])->name('pegawai.index');

  Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');

  Route::get('/set-error', function (Request $request) {
    // Ambil parameter
    $message = $request->query('message', 'Terjadi kesalahan tidak diketahui.');
    $redirect = $request->query('redirect', '/'); // default ke '/' kalau tidak ada

    // Simpan ke session
    session()->flash('error', $message);

    // Redirect ke halaman yang diminta
    return redirect()->route($redirect);
  });
});

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('login');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('auth-login');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');