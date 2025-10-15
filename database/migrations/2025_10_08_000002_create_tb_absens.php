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
    Schema::create('absens', function (Blueprint $table) {
      $table->engine = 'InnoDB';

      $table->id();
      $table->string('no_pegawai')->nullable();
      $table->string('shift')->nullable();
      $table->string('pict')->nullable();
      $table->string('status')->nullable();
      $table->string('keterangan')->nullable();
      $table->timestamps();

      // 2. Definisikan Foreign Key Constraint
      $table->foreign('no_pegawai') // Kolom di tabel absens
        ->references('no_pegawai') // Merujuk ke kolom di tabel pegawais
        ->on('pegawais') // Di tabel pegawais
        ->onDelete('cascade')
        ->onUpdate('cascade'); // Opsi: Hapus absen jika pegawai dihapus
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('absens');
  }
};