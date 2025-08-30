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
        Schema::create('nagaris', function (Blueprint $table) {
            $table->id();
            $table->string('nagari', 150);           // nama nagari
            $table->string('kecamatan', 150);
            $table->string('kabupaten', 150);
            $table->string('provinsi', 150);
            $table->string('kode_pos', 10)->nullable();
            $table->string('gambar')->nullable();    // path/URL gambar
            $table->unsignedBigInteger('total_penduduk')->nullable();
            $table->decimal('luas_wilayah', 10, 2)->nullable(); // km2
            $table->unsignedSmallInteger('tahun_pembentukan')->nullable(); // contoh: 1984
            $table->text('visi')->nullable();
            $table->json('misi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nagaris');
    }
};
