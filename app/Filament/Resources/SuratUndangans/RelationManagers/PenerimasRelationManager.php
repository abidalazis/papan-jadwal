<?php

namespace App\Filament\Resources\SuratUndangans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class PenerimasRelationManager extends RelationManager
{
    protected static string $relationship = 'penerimas';
    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $title = 'Daftar Penerima';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_hp')
                    ->label('Nomor HP')
                    ->copyable()
                    ->copyMessage('Nomor tersalin!')
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan'),
                Tables\Columns\IconColumn::make('pivot.status_kirim')
                    ->label('Status Kirim')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->headerActions([
                Actions\Action::make('tambahPenerima')
                    ->label('Tambah Penerima')
                    ->icon('heroicon-o-user-plus')
                    ->button()
                    ->color('primary')
                    ->form([
                        Select::make('penerima_id')
                            ->label('Pilih Penerima')
                            ->options(\App\Models\Penerima::query()->pluck('nama', 'id'))
                            ->searchable()
                            ->required(),
                        Toggle::make('status_kirim')
                            ->label('Sudah Dikirim?')
                            ->default(false),
                    ])
                    ->action(function (array $data, $livewire) {
                        $parent = $livewire->getOwnerRecord();
                        if ($parent->penerimas()->where('penerima_id', $data['penerima_id'])->exists()) {
                            Notification::make()
                                ->title('Penerima sudah terdaftar!')
                                ->danger()
                                ->send();
                            return;
                        }

                        $parent->penerimas()->attach($data['penerima_id'], [
                            'status_kirim' => $data['status_kirim'] ?? false,
                        ]);

                        Notification::make()
                            ->title('Penerima berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Actions\Action::make('kirim_wa')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record, $livewire) {
                        $undangan = $livewire->getOwnerRecord();
                        
                        // Format nomor HP
                        $nomorHP = preg_replace('/[^0-9]/', '', $record->nomor_hp);
                        if (substr($nomorHP, 0, 1) === '0') {
                            $nomorHP = '62' . substr($nomorHP, 1);
                        }
                        if (substr($nomorHP, 0, 2) !== '62') {
                            $nomorHP = '62' . $nomorHP;
                        }
                        
                        // Format pesan
                        $pesan = $this->formatPesan($undangan, $record);
                        $pesanEncoded = urlencode($pesan);
                        
                        return "https://wa.me/{$nomorHP}?text={$pesanEncoded}";
                    }, shouldOpenInNewTab: true)
                    ->after(function ($record, $livewire) {
                        // Update status kirim
                        $livewire->ownerRecord->penerimas()->updateExistingPivot($record->id, [
                            'status_kirim' => true
                        ]);
                        
                        Notification::make()
                            ->title('Link WhatsApp dibuka')
                            ->body('Status kirim diupdate menjadi terkirim')
                            ->success()
                            ->send();
                    }),
                    
                Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->pivot->status_kirim ? 'Tandai Belum Kirim' : 'Tandai Terkirim')
                    ->icon(fn ($record) => $record->pivot->status_kirim ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->pivot->status_kirim ? 'warning' : 'success')
                    ->action(function ($record, $livewire) {
                        $newStatus = !$record->pivot->status_kirim;
                        $livewire->ownerRecord->penerimas()->updateExistingPivot($record->id, [
                            'status_kirim' => $newStatus
                        ]);

                        Notification::make()
                            ->title('Status berhasil diubah')
                            ->body($newStatus ? 'Ditandai sebagai terkirim' : 'Ditandai sebagai belum kirim')
                            ->success()
                            ->send();
                    }),
                    
                Actions\Action::make('hapus')
                    ->label('Hapus dari Daftar')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function ($record, $livewire) {
                        $livewire->ownerRecord->penerimas()->detach($record->id);

                        Notification::make()
                            ->title('Penerima dihapus dari daftar')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
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