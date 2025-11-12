<?php

namespace App\Filament\Widgets;

use App\Models\SuratUndangan;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UndanganBesokWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('📅 Undangan Besok (' . Carbon::tomorrow()->format('d M Y') . ')')
            ->query(
                SuratUndangan::query()
                    ->whereDate('tanggal_acara', Carbon::tomorrow())
                    // ->with('penerimas')
            )
            ->columns([
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('judul')
                    ->label('Judul Acara')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('tanggal_acara')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('jumlah_penerima')
                    ->label('Jumlah Penerima')
                    ->getStateUsing(fn ($record) => $record->penerimas->count())
                    ->badge()
                    ->color('success'),
                TextColumn::make('status_kirim')
                ->label('Status Kirim')
                ->getStateUsing(function ($record) {
                    $total = $record->penerimas->count();
                    $terkirim = $record->penerimas->where('pivot.status_kirim', true)->count();
                    return "{$terkirim}/{$total} terkirim";
                })
                ->badge()
                ->color(function ($record) {
                    $total = $record->penerimas->count();
                    $terkirim = $record->penerimas->where('pivot.status_kirim', true)->count();
                    return $terkirim === $total ? 'success' : 'warning';
                }),
            ])
            ->emptyStateHeading('Tidak Ada Undangan Besok')
            ->emptyStateDescription('Tidak ada undangan yang dijadwalkan untuk besok.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('tanggal_acara', 'asc');
    }
}