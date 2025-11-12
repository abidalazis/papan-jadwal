<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratUndangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'judul',
        'keterangan',
        'tanggal_acara',
        'lokasi',
    ];
    public function penerimas()
{
    return $this->belongsToMany(Penerima::class, 'surat_penerima','surat_undangan_id', 'penerima_id')
                ->withPivot('status_kirim')
                ->withTimestamps()
                ->distinct(); // 🔥 ini mencegah hasil duplikat
}

}

