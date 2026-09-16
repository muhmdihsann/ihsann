<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi: Satu Provinsi memiliki banyak Kabupaten/Kota
     */
    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }

    /**
     * Relasi HasManyThrough: Mendapatkan semua data SPM melalui Kabupaten/Kota
     */
    public function spmData()
    {
        return $this->hasManyThrough(SpmData::class, Regency::class);
    }
}
