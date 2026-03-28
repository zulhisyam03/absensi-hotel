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
      $table->engine = 'InnoDB';

      $table->id();
      $table->string('no_pegawai')->unique();
      $table->string('nik');
      $table->string('npwp');
      $table->string('bpjs');
      $table->string('nama_pegawai');
      $table->string('no_hp', '14');
      $table->string('emergency_number', '14');
      $table->string('email')->unique();
      $table->string('departemen');
      $table->string('alamat');
      $table->string('tempat_lahir');
      $table->char('jenis_kelamin', 1);
      $table->date('tgl_lahir');
      $table->date('tgl_join');
      $table->string('jabatan', '15');
      $table->string('status_pegawai');
      $table->integer('last_salary');
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
