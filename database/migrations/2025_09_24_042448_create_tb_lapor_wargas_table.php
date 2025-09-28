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
        Schema::create('tb_lapor_wargas', function (Blueprint $table) {
            $table->id();
            $table->integer('kategori');
            $table->text('lokasi');
            $table->text('deskripsi');
            $table->string('foto', 255)->nullable();
            $table->string('status', 50)->default('request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lapor_wargas');
    }
};
