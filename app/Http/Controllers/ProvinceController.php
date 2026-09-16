<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        // Mengambil semua data provinsi, diurutkan berdasarkan nama
        $provinces = Province::orderBy('name', 'asc')->get();
        return view('provinces.index', compact('provinces'));
    }
}
