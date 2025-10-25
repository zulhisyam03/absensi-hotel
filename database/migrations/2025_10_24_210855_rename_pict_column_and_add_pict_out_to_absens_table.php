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
    Schema::table('absens', function (Blueprint $table) {
      // Ubah nama kolom pict -> pict_in
      $table->renameColumn('pict', 'pict_in');

      // Tambah kolom pict_out
      $table->string('pict_out')->nullable()->after('pict_in');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('absens', function (Blueprint $table) {
      // Kembalikan perubahan jika rollback
      $table->renameColumn('pict_in', 'pict');
      $table->dropColumn('pict_out');
    });
  }
};