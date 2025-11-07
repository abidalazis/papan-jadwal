<?php

namespace App\Filament\Resources\SuratUndangans;

use App\Filament\Resources\SuratUndangans\Pages\CreateSuratUndangan;
use App\Filament\Resources\SuratUndangans\Pages\EditSuratUndangan;
use App\Filament\Resources\SuratUndangans\Pages\ListSuratUndangans;
use App\Filament\Resources\SuratUndangans\Pages\ViewSuratUndangan;
use App\Filament\Resources\SuratUndangans\Schemas\SuratUndanganForm;
use App\Filament\Resources\SuratUndangans\Schemas\SuratUndanganInfolist;
use App\Filament\Resources\SuratUndangans\Tables\SuratUndangansTable;
use App\Models\SuratUndangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SuratUndanganResource extends Resource
{
    protected static ?string $model = SuratUndangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return SuratUndanganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SuratUndanganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratUndangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\SuratUndangans\RelationManagers\PenerimasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuratUndangans::route('/'),
            'create' => CreateSuratUndangan::route('/create'),
            'view' => ViewSuratUndangan::route('/{record}'),
            'edit' => EditSuratUndangan::route('/{record}/edit'),
        ];
    }
}
