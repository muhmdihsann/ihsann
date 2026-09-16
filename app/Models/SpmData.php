<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpmData extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit karena format plural bawaan Laravel
    // bisa salah membaca 'spm_data' menjadi 'spm_datas'.
    protected $table = 'spm_data';

    protected $guarded = ['id'];

    /**
     * Relasi: Data SPM ini milik satu Kabupaten/Kota
     */
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * Relasi: Data SPM ini berasal dari satu riwayat Import (bisa null jika input manual)
     */
    public function importHistory()
    {
        return $this->belongsTo(ImportHistory::class);
    }
}
