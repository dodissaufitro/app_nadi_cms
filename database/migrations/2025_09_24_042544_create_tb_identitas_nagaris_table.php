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
        Schema::create('tb_identitas_nagaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('kecamatan', 150);
            $table->string('kabupaten', 150);
            $table->string('provinsi', 150);
            $table->integer('kode_pos');
            $table->integer('total_penduduk');
            $table->integer('luas_wilayah');
            $table->integer('tahun_pembentukan');
            $table->text('visi');
            $table->text('misi');
            $table->string('foto', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_identitas_nagaris');
    }
};
