<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use App\Services\SpmDatasetImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;
use Throwable;

class DatasetImportController extends Controller
{
    private const PreviewSessionKey = 'spm-import-preview';

    public function index(): View
    {
        return view('import.index');
    }

    public function preview(Request $request, SpmDatasetImportService $importer): View
    {
        $validated = $request->validate(['file' => ['required', File::types(['xls', 'xlsx', 'csv'])->max('10mb')], 'year' => ['required', 'integer', 'digits:4']]);
        $file = $request->file('file');
        $path = $file->store('import-previews', 'local');
        $preview = $importer->validateRows($importer->readRows($path));
        $request->session()->put(self::PreviewSessionKey, ['path' => $path, 'filename' => $file->getClientOriginalName(), 'year' => $validated['year']]);

        return view('import.preview', ['filename' => $file->getClientOriginalName(), 'year' => $validated['year'], 'totalRows' => count($preview['valid']) + count($preview['invalid']), 'validData' => $preview['valid'], 'invalidData' => $preview['invalid']]);
    }

    public function store(Request $request, SpmDatasetImportService $importer): RedirectResponse
    {
        $preview = $request->session()->pull(self::PreviewSessionKey);
        if (! is_array($preview) || ! Storage::disk('local')->exists($preview['path'])) {
            return redirect()->route('import.index')->with('error', 'Preview import tidak ditemukan. Silakan unggah file kembali.');
        }
        try {
            $validation = $importer->validateRows($importer->readRows($preview['path']));
            if ($validation['invalid'] !== []) {
                Storage::disk('local')->delete($preview['path']);

                return redirect()->route('import.index')->with('error', 'Import dibatalkan karena file memiliki baris tidak valid.');
            }
            $history = $importer->import($validation['valid'], $preview['year'], $preview['filename'], $request->user()->id);
            Storage::disk('local')->delete($preview['path']);

            return redirect()->route('dashboard')->with('success', "Dataset {$history->year} berhasil diimpor sebagai draft.");
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('import.index')->with('error', 'Gagal mengimpor dataset. Tidak ada data final yang disimpan.');
        }
    }

    public function publish(ImportHistory $importHistory): RedirectResponse
    {
        $importHistory->update(['status' => ImportHistory::Published]);

        return back()->with('success', "Dataset {$importHistory->year} telah dipublikasikan.");
    }

    public function draft(ImportHistory $importHistory): RedirectResponse
    {
        $importHistory->update(['status' => ImportHistory::Draft]);

        return back()->with('success', "Dataset {$importHistory->year} dikembalikan ke draft.");
    }

    public function destroy(ImportHistory $importHistory): RedirectResponse
    {
        DB::transaction(function () use ($importHistory): void {
            $importHistory->spmData()->delete();
            $importHistory->delete();
        });

        return back()->with('success', 'Dataset beserta seluruh data hasil import telah dihapus.');
    }
}
