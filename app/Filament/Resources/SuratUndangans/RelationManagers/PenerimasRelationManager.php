<?php

namespace App\Filament\Resources\SuratUndangans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;


class PenerimasRelationManager extends RelationManager
{
    // nama relasi sesuai method di model SuratUndangan
    protected static string $relationship = 'penerimas';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $title = 'Daftar Penerima';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nomor_hp')->label('Nomor HP'),
                Tables\Columns\TextColumn::make('jabatan')->label('Jabatan'),
                Tables\Columns\TextColumn::make('pivot.status_kirim')
                    ->label('Status Kirim')
                    ->formatStateUsing(fn ($state) => $state ? 'Terkirim' : 'Belum')
                    ->toggleable(false),
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
            ->recordActions([ // Ganti dari ->actions()
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
            ->bulkActions([]); // Ganti dari ->bulkActions([])
    }
}
