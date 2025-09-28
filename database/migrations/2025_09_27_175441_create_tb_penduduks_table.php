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
        Schema::create('tb_penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique()->comment('Nomor Induk Kependudukan');
            $table->string('nama', 255)->comment('Nama Lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('Jenis Kelamin: L = Laki-laki, P = Perempuan');
            $table->date('tgl_lahir')->comment('Tanggal Lahir');
            $table->string('tempat_lahir', 100)->comment('Tempat Lahir');
            $table->string('no_kk', 16)->comment('Nomor Kartu Keluarga');
            $table->string('nama_ayah', 255)->nullable()->comment('Nama Ayah');
            $table->string('nama_ibu', 255)->nullable()->comment('Nama Ibu');
            $table->string('foto', 255)->nullable()->comment('Path foto penduduk');
            $table->text('alamat')->comment('Alamat Lengkap');
            $table->string('dusun', 100)->comment('Nama Dusun');
            $table->string('rt', 10)->comment('RT');
            $table->string('rw', 10)->comment('RW');
            $table->string('pendidikan', 50)->comment('Tingkat Pendidikan');
            $table->integer('umur')->comment('Umur dalam tahun');
            $table->string('pekerjaan', 100)->comment('Jenis Pekerjaan');
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'janda', 'duda'])->comment('Status Pernikahan');
            $table->date('tgl_terdaftar')->comment('Tanggal Terdaftar');
            $table->timestamps();

            // Indexes for better performance
            $table->index('nik');
            $table->index('no_kk');
            $table->index('dusun');
            $table->index(['rt', 'rw']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penduduks');
    }
};
