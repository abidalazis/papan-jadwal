<?php

namespace App\Filament\Resources\SuratUndangans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class SuratUndanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomor_surat')
                    ->required()
                    ->unique(ignorable: fn ($record) => $record), // <--- kunci unik,
                TextInput::make('judul')
                    ->required(),
                DateTimePicker::make('tanggal_acara')
                    ->label('Tanggal Acara')
                    ->required(),
                TextInput::make('lokasi')
                    ->default(null),
                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(),
                    // 🧩 Tambahkan relasi ke penerima di sini:
                Select::make('penerimas')
                ->label('Penerima Surat')
                ->multiple()
                ->relationship('penerimas', 'nama') // relasi di model SuratUndangan
                ->preload(), // agar data penerima langsung muncul di dropdown
            ]);
    }
}
