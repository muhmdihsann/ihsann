<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi: Kabupaten/Kota ini milik satu Provinsi
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Relasi: Satu Kabupaten/Kota memiliki banyak Data SPM (berdasarkan tahun)
     */
    public function spmData()
    {
        return $this->hasMany(SpmData::class);
    }
}
