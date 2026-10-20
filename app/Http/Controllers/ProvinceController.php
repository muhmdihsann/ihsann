<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\SpmData;
use App\Services\PublicMapDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function index(Request $request): View
    {
        $provinces = Province::query()
            ->withCount('regencies', 'spmData')
            ->withCount(['spmData as publishable_spm_data_count' => fn (Builder $query): Builder => $query->whereHas('importHistory', fn (Builder $query): Builder => $query->where('status', 'published'))])
            ->withCount(['spmData as published_spm_data_count' => fn (Builder $query): Builder => $query->published()])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->trim()->toString();
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('provinces.index', compact('provinces'));
    }

    public function edit(Request $request, Province $province, PublicMapDataService $mapDataService): View
    {
        $availableYears = $mapDataService->availableYears();
        if ($availableYears->isEmpty()) {
            $availableYears = SpmData::query()
                ->whereHas('regency', fn (Builder $query): Builder => $query->where('province_id', $province->id))
                ->select('year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
        }
        $requestedYear = $request->integer('year');
        $year = $availableYears->contains($requestedYear) ? $requestedYear : $availableYears->first();
        $mapPayload = $mapDataService->data($year);
        $provinceData = collect($mapPayload['provinces'])->firstWhere('id', $province->id);
        $spmRows = SpmData::query()
            ->with(['importHistory', 'regency'])
            ->where('year', $year)
            ->whereHas('regency', fn (Builder $query): Builder => $query->where('province_id', $province->id))
            ->orderBy('regency_id')
            ->get();

        return view('provinces.edit', compact('province', 'availableYears', 'year', 'provinceData', 'spmRows'));
    }

    public function update(Request $request, Province $province): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:5', Rule::unique('provinces', 'code')->ignore($province->id)],
        ]);

        $province->update($validated);

        return redirect()->route('provinces.index')->with('success', 'Data provinsi berhasil diperbarui.');
    }

    public function publish(Province $province): RedirectResponse
    {
        $updated = SpmData::query()
            ->whereHas('regency', fn (Builder $query): Builder => $query->where('province_id', $province->id))
            ->whereHas('importHistory', fn (Builder $query): Builder => $query->where('status', 'published'))
            ->update(['is_published' => true]);

        if ($updated === 0) {
            return redirect()->route('provinces.index')->with('error', 'Tidak ada data SPM provinsi yang dapat dipublikasikan. Publish dataset import terlebih dahulu.');
        }

        return redirect()->route('provinces.index')->with('success', "{$updated} data SPM provinsi berhasil dipublikasikan.");
    }

    public function draft(Province $province): RedirectResponse
    {
        $updated = SpmData::query()
            ->whereHas('regency', fn (Builder $query): Builder => $query->where('province_id', $province->id))
            ->where('is_published', true)
            ->update(['is_published' => false]);

        return redirect()->route('provinces.index')->with('success', "{$updated} data SPM provinsi disembunyikan dari dashboard publik.");
    }

    public function destroy(Province $province): RedirectResponse
    {
        $name = $province->name;
        $province->delete();

        return redirect()->route('provinces.index')->with('success', "Provinsi {$name} dan seluruh data kabupaten/SPM terkait berhasil dihapus.");
    }
}
