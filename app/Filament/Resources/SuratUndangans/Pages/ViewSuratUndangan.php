<?php

namespace App\Filament\Resources\SuratUndangans\Pages;

use App\Filament\Resources\SuratUndangans\SuratUndanganResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ViewSuratUndangan extends ViewRecord
{
    protected static string $resource = SuratUndanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            
            Action::make('kirim_semua_wa')
                ->label('Kirim WA ke Semua Penerima')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->visible(fn () => $this->record->penerimas->count() > 0)
                ->requiresConfirmation()
                ->modalHeading('Kirim Pesan WhatsApp ke Semua Penerima')
                ->modalDescription(fn () => 
                    "Sistem akan membuka {$this->record->penerimas->count()} tab WhatsApp secara berurutan untuk acara \"{$this->record->judul}\". Pastikan pop-up blocker browser Anda tidak aktif."
                )
                ->modalSubmitActionLabel('Ya, Buka Semua Link')
                ->action(function () {
                    $penerimaList = [];
                    
                    foreach ($this->record->penerimas as $penerima) {
                        $nomorHP = $this->formatNomorHP($penerima->nomor_hp);
                        $pesan = $this->formatPesan($this->record, $penerima);
                        $pesanEncoded = urlencode($pesan);
                        
                        $penerimaList[] = [
                            'nama' => $penerima->nama,
                            'nomor' => $nomorHP,
                            'link' => "https://wa.me/{$nomorHP}?text={$pesanEncoded}"
                        ];
                        
                        // Update status kirim
                        $this->record->penerimas()->updateExistingPivot($penerima->id, [
                            'status_kirim' => true
                        ]);
                    }
                    
                    // Generate JavaScript untuk membuka semua link
                    $jsCode = "let links = " . json_encode(array_column($penerimaList, 'link')) . ";\n";
                    $jsCode .= "let index = 0;\n";
                    $jsCode .= "function openNextLink() {\n";
                    $jsCode .= "    if (index < links.length) {\n";
                    $jsCode .= "        window.open(links[index], '_blank');\n";
                    $jsCode .= "        index++;\n";
                    $jsCode .= "        if (index < links.length) {\n";
                    $jsCode .= "            setTimeout(openNextLink, 2000);\n"; // Delay 2 detik antar tab
                    $jsCode .= "        }\n";
                    $jsCode .= "    }\n";
                    $jsCode .= "}\n";
                    $jsCode .= "openNextLink();";
                    
                    // Inject JavaScript
                    $this->js($jsCode);
                    
                    Notification::make()
                        ->title('Link WhatsApp Dibuka!')
                        ->body("Membuka {$this->record->penerimas->count()} tab WhatsApp dengan delay 2 detik. Status kirim sudah diupdate.")
                        ->success()
                        ->duration(5000)
                        ->send();
                }),
                
            Action::make('preview_pesan')
                ->label('Preview Pesan')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->visible(fn () => $this->record->penerimas->count() > 0)
                ->modalHeading('Preview Pesan WhatsApp')
                ->modalContent(function () {
                    $penerima = $this->record->penerimas->first();
                    if ($penerima) {
                        $pesan = $this->formatPesan($this->record, $penerima);
                        return view('filament.modals.preview-pesan', [
                            'pesan' => $pesan,
                            'penerima' => $penerima
                        ]);
                    }
                    return null;
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
        ];
    }
    
    protected function formatNomorHP(string $nomor): string
    {
        // Hapus karakter non-digit
        $nomorHP = preg_replace('/[^0-9]/', '', $nomor);
        
        // Jika nomor diawali 0, ganti dengan 62
        if (substr($nomorHP, 0, 1) === '0') {
            $nomorHP = '62' . substr($nomorHP, 1);
        }
        
        // Jika tidak diawali 62, tambahkan
        if (substr($nomorHP, 0, 2) !== '62') {
            $nomorHP = '62' . $nomorHP;
        }
        
        return $nomorHP;
    }
    
    protected function formatPesan($undangan, $penerima): string
    {
        $tanggal = Carbon::parse($undangan->tanggal_acara)->locale('id')->isoFormat('dddd, D MMMM Y');
        
        return "🔔 *PENGINGAT UNDANGAN*\n\n" .
               "Yth. {$penerima->nama}" . ($penerima->jabatan ? " ({$penerima->jabatan})" : "") . "\n\n" .
               "Mengingatkan bahwa akan ada acara:\n\n" .
               "📋 *Acara:* {$undangan->judul}\n" .
               "📅 *Tanggal:* {$tanggal}\n" .
               "📍 *Lokasi:* " . ($undangan->lokasi ?? '-') . "\n" .
               ($undangan->keterangan ? "\n📝 *Keterangan:* {$undangan->keterangan}\n" : "") .
               "\n\nMohon kehadirannya. Terima kasih.\n\n" .
               "_Pesan ini dikirim otomatis dari Sistem Papan Jadwal Surat_";
    }
}