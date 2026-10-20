<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use App\Models\SpmData;
use App\Services\PublicMapDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpmDataController extends Controller
{
    public function index(Request $request): View
    {
        $years = SpmData::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $query = SpmData::query()->with(['regency.province', 'importHistory']);

        $query->when($request->filled('year'), fn (Builder $query): Builder => $query->where('year', $request->integer('year')));
        $query->when($request->filled('search'), function (Builder $query) use ($request): void {
            $search = $request->string('search')->trim()->toString();
            $query->whereHas('regency', function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('province', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%"));
            });
        });

        $spmData = $query->orderByDesc('year')->orderBy('id')->paginate(20)->withQueryString();
        $indexRouteName = $request->routeIs('regencies.index') ? 'regencies.index' : 'spm.index';

        return view('spm.index', compact('spmData', 'years', 'indexRouteName'));
    }

    public function edit(SpmData $spmData): View
    {
        $spmData->load(['regency.province', 'importHistory']);

        return view('spm.edit', compact('spmData'));
    }

    public function update(Request $request, SpmData $spmData, PublicMapDataService $mapData): RedirectResponse
    {
        $validated = $request->validate([
            'kelembagaan' => ['nullable', 'string', 'max:255'],
            'perencanaan_anggaran' => ['nullable', 'string', 'max:255'],
            'capaian_spm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'jml_kecamatan' => ['nullable', 'integer', 'min:0'],
            'jml_pos' => ['nullable', 'integer', 'min:0'],
            'pr_pos_kecamatan' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'total_sdm' => ['nullable', 'integer', 'min:0'],
            'sdm_sertifikat' => ['nullable', 'integer', 'min:0'],
            'pr_sdm_sertifikat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'jml_desa' => ['nullable', 'integer', 'min:0'],
            'jml_redkar' => ['nullable', 'integer', 'min:0'],
            'pr_redkar' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'dimensi_kelembagaan' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'dimensi_perencanaan' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'dimensi_capaian' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'dimensi_sarpras' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'dimensi_sdm_sertifikat' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'dimensi_pemberdayaan' => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        $dimensions = [
            'dimensi_kelembagaan',
            'dimensi_perencanaan',
            'dimensi_capaian',
            'dimensi_sarpras',
            'dimensi_sdm_sertifikat',
            'dimensi_pemberdayaan',
        ];
        $validated['nilai_akhir'] = round(min(100, array_sum(array_map(
            fn (string $field): float => (float) ($validated[$field] ?? 0),
            $dimensions,
        ))), 2);
        $validated['kategori'] = $mapData->categoryFor($validated['nilai_akhir']);

        $spmData->fill($validated);
        $spmData->is_published = false;
        $spmData->save();

        return redirect()->route($this->returnRoute($request), $this->returnQuery($request))
            ->with('success', 'Perubahan disimpan sebagai draft. Publish data agar perubahan tampil di dashboard publik.');
    }

    public function publish(SpmData $spmData): RedirectResponse
    {
        if ($spmData->importHistory?->status !== ImportHistory::Published) {
            return back()->with('error', 'Publish dataset import terlebih dahulu sebelum memublikasikan data kabupaten/kota.');
        }

        $spmData->update(['is_published' => true]);

        return redirect()->route($this->returnRoute(request()), $this->returnQuery(request()))
            ->with('success', 'Data SPM berhasil dipublikasikan ke dashboard publik.');
    }

    public function draft(SpmData $spmData): RedirectResponse
    {
        $spmData->update(['is_published' => false]);

        return redirect()->route($this->returnRoute(request()), $this->returnQuery(request()))
            ->with('success', 'Data SPM disembunyikan dari dashboard publik.');
    }

    public function destroy(SpmData $spmData): RedirectResponse
    {
        $name = $spmData->regency->name;
        $spmData->delete();

        return redirect()->route($this->returnRoute(request()), $this->returnQuery(request()))
            ->with('success', "Data SPM {$name} berhasil dihapus.");
    }

    private function returnRoute(Request $request): string
    {
        return match ($request->input('return_route')) {
            'regencies.index' => 'regencies.index',
            'provinces.edit' => $request->filled('return_province_id') ? 'provinces.edit' : 'spm.index',
            default => 'spm.index',
        };
    }

    /** @return array<string, int|string> */
    private function returnQuery(Request $request): array
    {
        return array_filter([
            'province' => $request->input('return_route') === 'provinces.edit' ? $request->integer('return_province_id') : null,
            'year' => $request->input('year'),
            'search' => $request->input('search'),
        ], fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
