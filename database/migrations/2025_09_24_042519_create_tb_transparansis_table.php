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
        Schema::create('tb_transparansis', function (Blueprint $table) {
            $table->id(); // id integer auto increment
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->string('alokasi', 100);
            $table->string('realisasi', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transparansis');
    }
};
