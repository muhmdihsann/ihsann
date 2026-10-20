<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportHistory extends Model
{
    use HasFactory;

    public const Draft = 'draft';

    public const Published = 'published';

    protected $guarded = ['id'];

    /**
     * Relasi: Riwayat import ini dilakukan oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu riwayat import menghasilkan banyak baris Data SPM
     */
    public function spmData(): HasMany
    {
        return $this->hasMany(SpmData::class);
    }
}
