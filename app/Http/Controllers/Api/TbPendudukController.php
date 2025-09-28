<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TbPenduduk;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TbPendudukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $penduduk = TbPenduduk::select([
                'id',
                'nik',
                'nama',
                'jenis_kelamin',
                'tempat_lahir',
                'tgl_lahir',
                'umur',
                'alamat',
                'dusun',
                'rt',
                'rw',
                'pendidikan',
                'pekerjaan',
                'status_pernikahan',
                'foto'
            ])
                ->orderBy('nama', 'asc')
                ->paginate(50);

            return response()->json([
                'success' => true,
                'message' => 'Data penduduk berhasil diambil',
                'data' => $penduduk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penduduk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get population statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalPenduduk = TbPenduduk::count();
            $jumlahLakiLaki = TbPenduduk::where('jenis_kelamin', 'L')->count();
            $jumlahPerempuan = TbPenduduk::where('jenis_kelamin', 'P')->count();

            // Statistik berdasarkan kelompok umur
            $anakAnak = TbPenduduk::where('umur', '<', 17)->count();
            $usiaProduktif = TbPenduduk::whereBetween('umur', [17, 59])->count();
            $lansia = TbPenduduk::where('umur', '>=', 60)->count();

            // Statistik berdasarkan status pernikahan
            $belumMenikah = TbPenduduk::where('status_pernikahan', 'belum_menikah')->count();
            $menikah = TbPenduduk::where('status_pernikahan', 'menikah')->count();
            $janda = TbPenduduk::where('status_pernikahan', 'janda')->count();
            $duda = TbPenduduk::where('status_pernikahan', 'duda')->count();

            // Statistik berdasarkan dusun
            $perDusun = TbPenduduk::selectRaw('dusun, COUNT(*) as jumlah')
                ->groupBy('dusun')
                ->orderBy('jumlah', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Statistik penduduk berhasil diambil',
                'data' => [
                    'total_penduduk' => $totalPenduduk,
                    'jenis_kelamin' => [
                        'laki_laki' => $jumlahLakiLaki,
                        'perempuan' => $jumlahPerempuan,
                        'persentase_laki_laki' => $totalPenduduk > 0 ? round(($jumlahLakiLaki / $totalPenduduk) * 100, 2) : 0,
                        'persentase_perempuan' => $totalPenduduk > 0 ? round(($jumlahPerempuan / $totalPenduduk) * 100, 2) : 0,
                    ],
                    'kelompok_umur' => [
                        'anak_anak' => $anakAnak,
                        'usia_produktif' => $usiaProduktif,
                        'lansia' => $lansia,
                    ],
                    'status_pernikahan' => [
                        'belum_menikah' => $belumMenikah,
                        'menikah' => $menikah,
                        'janda' => $janda,
                        'duda' => $duda,
                    ],
                    'per_dusun' => $perDusun,
                    'updated_at' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik penduduk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get population summary (quick stats)
     */
    public function summary(): JsonResponse
    {
        try {
            $totalPenduduk = TbPenduduk::count();
            $jumlahLakiLaki = TbPenduduk::where('jenis_kelamin', 'L')->count();
            $jumlahPerempuan = TbPenduduk::where('jenis_kelamin', 'P')->count();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan data penduduk berhasil diambil',
                'data' => [
                    'total_penduduk' => $totalPenduduk,
                    'jumlah_laki_laki' => $jumlahLakiLaki,
                    'jumlah_perempuan' => $jumlahPerempuan,
                    'rasio_jenis_kelamin' => $jumlahPerempuan > 0 ? round($jumlahLakiLaki / $jumlahPerempuan, 2) : 0,
                    'updated_at' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan data penduduk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $penduduk = TbPenduduk::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail penduduk berhasil diambil',
                'data' => $penduduk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Penduduk tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
