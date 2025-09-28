<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // GET /api/laporans
    public function index()
    {
        return response()->json(Laporan::latest()->get());
    }

    // POST /api/laporans
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('laporan-foto', 'public');
        }

        $laporan = Laporan::create($validated);

        return response()->json([
            'message' => 'Laporan berhasil dibuat',
            'data' => $laporan,
        ], 201);
    }

    // GET /api/laporans/{id}
    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);
        return response()->json($laporan);
    }

    // PUT /api/laporans/{id}
    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // hapus foto lama
            if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('public/laporan', 'public');
        }

        $laporan->update($validated);

        return response()->json([
            'message' => 'Laporan berhasil diperbarui',
            'data' => $laporan,
        ]);
    }

    // DELETE /api/laporans/{id}
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);

        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus',
        ]);
    }
}
