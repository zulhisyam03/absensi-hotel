<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
    public function up()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
            $table->string('no_kk')->nullable()->change();
            $table->string('npwp')->nullable()->change();
            $table->string('bpjs')->nullable()->change();
            $table->string('bpjs_tk')->nullable()->change();
            $table->string('emergency_number')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('nik')->nullable(false)->change();
            $table->string('no_kk')->nullable(false)->change();
            $table->string('npwp')->nullable(false)->change();
            $table->string('bpjs')->nullable(false)->change();
            $table->string('bpjs_tk')->nullable(false)->change();
            $table->string('emergency_number')->nullable(false)->change();
        });
    }

};