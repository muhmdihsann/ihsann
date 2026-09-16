<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpmData;

class SpmDataController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar tahun yang ada di database untuk dropdown filter
        $years = SpmData::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        // Mulai query untuk mengambil data SPM beserta relasi Kabupaten dan Provinsi
        $query = SpmData::with(['regency.province']);

        // 1. Filter berdasarkan Tahun (jika dipilih)
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // 2. Filter berdasarkan Pencarian Nama Kabupaten/Kota
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('regency', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Tampilkan data dengan pagination (20 data per halaman)
        $spmData = $query->paginate(20)->withQueryString();

        return view('spm.index', compact('spmData', 'years'));
    }
    // ... (Fungsi index yang sudah ada biarkan saja) ...

    // 1. Menampilkan halaman form edit
    public function edit($id)
    {
        // Cari data SPM berdasarkan ID, sertakan relasi kabupatennya
        $spmData = SpmData::with(['regency.province'])->findOrFail($id);

        return view('spm.edit', compact('spmData'));
    }

    // 2. Memproses penyimpanan data hasil edit
    public function update(Request $request, $id)
    {
        $request->validate([
            'nilai_akhir' => 'required|numeric',
            'kategori' => 'required|string',
            'jml_pos' => 'required|numeric',
            'total_sdm' => 'required|numeric',
        ]);

        $spmData = SpmData::findOrFail($id);

        // Update data ke database
        $spmData->update([
            'nilai_akhir' => $request->nilai_akhir,
            'kategori' => $request->kategori,
            'jml_pos' => $request->jml_pos,
            'total_sdm' => $request->total_sdm,
        ]);

        return redirect('/admin/spm')->with('success', 'Data capaian SPM wilayah ' . $spmData->regency->name . ' berhasil diperbarui!');
    }

    // 3. Menghapus data SPM
    public function destroy($id)
    {
        $spmData = SpmData::findOrFail($id);
        $namaWilayah = $spmData->regency->name;

        $spmData->delete();

        return redirect('/admin/spm')->with('success', 'Data SPM wilayah ' . $namaWilayah . ' berhasil dihapus!');
    }
}
