<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('pegawais', function (Blueprint $table) {
      $table->id();
      $table->string('no_karyawan')->unique();
      $table->string('nama_pegawai');
      $table->string('no_hp', '14');
      $table->string('email');
      $table->string('tempat_lahir');
      $table->date('tgl_lahir');
      $table->date('tgl_join');
      $table->string('status');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pegawais');
  }
};