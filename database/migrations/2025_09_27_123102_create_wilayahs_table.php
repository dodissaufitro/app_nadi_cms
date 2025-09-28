<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wilayahs', function (Blueprint $table) {
            $table->id();
            $table->string('dusun')->nullable();
            $table->string('kawil')->nullable();
            $table->string('nik_kawil')->nullable();
            $table->string('nama_kawil')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->string('kk')->nullable();
            $table->string('laki_laki')->nullable();
            $table->string('perempuan')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayahs');
    }
};
