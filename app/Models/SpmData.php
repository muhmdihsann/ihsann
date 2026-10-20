<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmData extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit karena format plural bawaan Laravel
    // bisa salah membaca 'spm_data' menjadi 'spm_datas'.
    protected $table = 'spm_data';

    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Relasi: Data SPM ini milik satu Kabupaten/Kota
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * Relasi: Data SPM ini berasal dari satu riwayat Import (bisa null jika input manual)
     */
    public function importHistory(): BelongsTo
    {
        return $this->belongsTo(ImportHistory::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('spm_data.is_published', true)
            ->whereHas('importHistory', function (Builder $query): void {
                $query->where('status', ImportHistory::Published);
            });
    }
}
