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
    Schema::create('shift_kerjas', function (Blueprint $table) {
      $table->engine = 'InnoDB';

      $table->id();
      $table->string('no_pegawai');
      $table->string('shift');
      $table->time('waktu_masuk');
      $table->time('waktu_pulang');
      $table->char('flag');
      $table->timestamps();

      // 2. Definisikan Foreign Key Constraint
      $table->foreign('no_pegawai') // Kolom di tabel shift_kerjas
        ->references('no_pegawai') // Merujuk ke kolom di tabel pegawais
        ->on('pegawais') // Di tabel pegawais
        ->onDelete('cascade')
        ->onUpdate('cascade'); // Opsi: Hapus shift jika pegawai dihapus
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('shift_kerjas');
  }
};