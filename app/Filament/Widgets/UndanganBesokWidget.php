<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables\Table;
use App\Models\SuratUndangan;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
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
                    TextColumn::make('penerimas')
                    ->label('Penerima')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if ($record->penerimas->isEmpty()) {
                            return new HtmlString('<span class="text-gray-400">Belum ada penerima</span>');
                        }
                        
                        // Ambil nama-nama penerima
                        $names = $record->penerimas->pluck('nama')->toArray();
                        
                        // Jika lebih dari 3, tampilkan 3 nama pertama + badge jumlah sisanya
                        if (count($names) > 3) {
                            $displayed = array_slice($names, 0, 3);
                            $remaining = count($names) - 3;
                            
                            $html = '<div class="space-y-1">';
                            foreach ($displayed as $name) {
                                $html .= '<div class="text-sm">• ' . e($name) . '</div>';
                            }
                            $html .= '<div class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">';
                            $html .= '+' . $remaining . ' lainnya';
                            $html .= '</div>';
                            $html .= '</div>';
                            
                            return new HtmlString($html);
                        }
                        
                        // Jika 3 atau kurang, tampilkan semua
                        $html = '<div class="space-y-1">';
                        foreach ($names as $name) {
                            $html .= '<div class="text-sm">• ' . e($name) . '</div>';
                        }
                        $html .= '</div>';
                        
                        return new HtmlString($html);
                    })
                    ->tooltip(function ($record) {
                        // Tooltip menampilkan semua nama lengkap dengan jabatan
                        if ($record->penerimas->isEmpty()) {
                            return null;
                        }
                        
                        return $record->penerimas->map(function ($penerima) {
                            return $penerima->nama . ($penerima->jabatan ? ' (' . $penerima->jabatan . ')' : '');
                        })->implode("\n");
                    }),
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
            ->recordUrl(fn ($record) => route('filament.admin.resources.surat-undangans.view', ['record' => $record]))
            ->emptyStateHeading('Tidak Ada Undangan Besok')
            ->emptyStateDescription('Tidak ada undangan yang dijadwalkan untuk besok.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('tanggal_acara', 'asc');
    }
}