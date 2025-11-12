<?php

namespace App\Console\Commands;

use App\Models\SuratUndangan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DebugPenerimaData extends Command
{
    protected $signature = 'debug:penerima {surat_id?}';
    
    protected $description = 'Debug data penerima untuk undangan';

    public function handle()
    {
        $suratId = $this->argument('surat_id');
        
        if ($suratId) {
            $undangan = SuratUndangan::find($suratId);
        } else {
            // Ambil undangan besok
            $undangan = SuratUndangan::whereDate('tanggal_acara', Carbon::tomorrow())->first();
        }
        
        if (!$undangan) {
            $this->error('Undangan tidak ditemukan');
            return 1;
        }
        
        $this->info("🔍 Debug Undangan: {$undangan->judul} (ID: {$undangan->id})");
        $this->newLine();
        
        // Load penerimas
        $undangan->load('penerimas');
        
        $this->line("Total penerimas dari relasi: " . $undangan->penerimas->count());
        $this->line("Total penerimas unique (by id): " . $undangan->penerimas->unique('id')->count());
        $this->line("Total penerimas unique (by nama): " . $undangan->penerimas->unique('nama')->count());
        $this->newLine();
        
        $this->info("📋 Daftar Penerimas:");
        $this->table(
            ['ID', 'Nama', 'Nomor HP', 'Jabatan', 'Status Kirim'],
            $undangan->penerimas->map(function ($p) {
                return [
                    $p->id,
                    $p->nama,
                    $p->nomor_hp,
                    $p->jabatan ?? '-',
                    $p->pivot->status_kirim ? '✅' : '❌'
                ];
            })
        );
        
        $this->newLine();
        $this->info("🔗 Data Pivot (surat_penerima):");
        $pivotData = \DB::table('surat_penerima')
            ->where('surat_undangan_id', $undangan->id)
            ->get();
            
        $this->table(
            ['ID', 'Surat ID', 'Penerima ID', 'Status Kirim', 'Created'],
            $pivotData->map(function ($p) {
                return [
                    $p->id,
                    $p->surat_undangan_id,
                    $p->penerima_id,
                    $p->status_kirim ? '✅' : '❌',
                    $p->created_at
                ];
            })
        );
        
        return 0;
    }
}