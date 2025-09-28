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
        Schema::create('tb_beritas', function (Blueprint $table) {
            $table->id();
            $table->string('konten', 255);
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->date('tanggal');
            $table->string('foto', 255)->nullable();
            $table->string('status', 50)->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_beritas');
    }
};
