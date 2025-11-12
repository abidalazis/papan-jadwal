<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicatePenerima extends Command
{
    protected $signature = 'cleanup:duplicate-penerima';
    
    protected $description = 'Hapus duplikasi data di tabel pivot surat_penerima';

    public function handle()
    {
        $this->info('🔍 Mencari duplikasi data...');
        
        // Cari duplikasi di tabel pivot
        $duplicates = DB::table('surat_penerima')
            ->select('surat_undangan_id', 'penerima_id', DB::raw('COUNT(*) as count'))
            ->groupBy('surat_undangan_id', 'penerima_id')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->info('✅ Tidak ada duplikasi data.');
            return 0;
        }
        
        $this->warn("⚠️  Ditemukan {$duplicates->count()} duplikasi data.");
        
        $totalDeleted = 0;
        
        foreach ($duplicates as $duplicate) {
            $this->line("Membersihkan: Surat #{$duplicate->surat_undangan_id} - Penerima #{$duplicate->penerima_id}");
            
            // Ambil semua record duplikat
            $records = DB::table('surat_penerima')
                ->where('surat_undangan_id', $duplicate->surat_undangan_id)
                ->where('penerima_id', $duplicate->penerima_id)
                ->orderBy('id', 'asc')
                ->get();
            
            // Simpan yang pertama, hapus sisanya
            $keepId = $records->first()->id;
            
            $deleted = DB::table('surat_penerima')
                ->where('surat_undangan_id', $duplicate->surat_undangan_id)
                ->where('penerima_id', $duplicate->penerima_id)
                ->where('id', '!=', $keepId)
                ->delete();
            
            $totalDeleted += $deleted;
            $this->comment("  → Dihapus: {$deleted} record duplikat");
        }
        
        $this->newLine();
        $this->info("✅ Selesai! Total {$totalDeleted} record duplikat berhasil dihapus.");
        
        return 0;
    }
}