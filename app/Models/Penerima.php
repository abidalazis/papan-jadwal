<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerima extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'nomor_hp',
    ];

    public function suratUndangans()
    {
        return $this->belongsToMany(SuratUndangan::class, 'surat_penerima','penerima_id', 'surat_undangan_id')
                    ->withPivot('status_kirim')
                    ->withTimestamps();
    }

    // public function suratUndangans(): HasMany
    // {
    //     return $this->hasMany(SuratUndangan::class);
    // }

}