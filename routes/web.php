<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\UserController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\pages\MapController;
use App\Http\Controllers\pages\ShiftKerjaController;
use App\Http\Controllers\pages\PegawaiController;
use App\Http\Controllers\pages\PayrollController;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DataTableController;
use Illuminate\Support\Facades\Auth;

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

  // Pegawai
  Route::get('/pages/daftar-pegawai', [PegawaiController::class, 'index'])->name('pages-pegawai');
  Route::get('/pages/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
  Route::get('/pages/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
  // Route baru: Search nama pegawai untuk autocomplete (GET)
  Route::get('/pegawai/search', [PegawaiController::class, 'searchPegawai'])->name('pegawai.search');

  // Shift Kerja
  Route::get('/pages/shift-kerja', [ShiftKerjaController::class, 'index'])->name('pages-shift-kerja');
  Route::get('/pages/shift-kerja/create', [ShiftKerjaController::class, 'create'])->name('pages-shift-kerja.create');
  Route::get('/pages/shift-kerja/{id}/edit', [ShiftKerjaController::class, 'edit'])->name('pages-shift-kerja.edit');
  Route::get('/config/shift-kerja', [ShiftKerjaController::class, 'viewConfig'])->name('config-shift-kerja');
  Route::post('/config/shift-kerja/store', [ShiftKerjaController::class, 'storeParameter'])->name('config-shift-kerja.store');
  Route::get('/config/shift-kerja/{id}/edit', [ShiftKerjaController::class, 'editParameter'])->name('config-shift-kerja.edit');

  // Payroll
  Route::get('/pages/payroll', [PayrollController::class, 'index'])->name('pages-payroll');

  // Lokasi
  Route::get('config/lokasi', [MapController::class, 'index'])->name('config-lokasi');
  Route::post('config/lokasi/store', [MapController::class, 'store'])->name('config-lokasi.store');

  // user
  Route::get("config/user", [UserController::class, 'index'])->name('config-user');

  // DataTables AJAX route
  Route::get('/datatable/history-absen', [DataTableController::class, 'index'])->name('history-absen.index');
  Route::get('/datatable/shift-kerja', [DataTableController::class, 'viewShift'])->name('shift-kerja.index');
  Route::get('/datatable/param-shift-kerja', [DataTableController::class, 'viewParamShiftKerja'])->name('param-shift-kerja.index');
  Route::get('/datatable/pegawai', [DataTableController::class, 'viewPegawai'])->name('pegawai.index');

  Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');
});

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('login');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('auth-login');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');