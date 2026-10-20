<?php

namespace App\Http\Controllers;

use App\Exports\SpmReportExport;
use App\Models\SpmData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index(): View
    {
        $years = SpmData::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');

        return view('export.index', compact('years'));
    }

    public function exportExcel(Request $request)
    {
        $availableYears = SpmData::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');
        $validated = $request->validate([
            'year' => ['required', 'integer', Rule::in($availableYears->all())],
        ]);
        $year = (int) $validated['year'];

        return Excel::download(new SpmReportExport($year), "Laporan_SPM_Kebakaran_Tahun_{$year}.xlsx");
    }
}
