<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi: Riwayat import ini dilakukan oleh satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu riwayat import menghasilkan banyak baris Data SPM
     */
    public function spmData()
    {
        return $this->hasMany(SpmData::class);
    }
}
