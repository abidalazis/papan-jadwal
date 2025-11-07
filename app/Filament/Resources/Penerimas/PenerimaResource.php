<?php

namespace App\Filament\Resources\Penerimas;

use App\Filament\Resources\Penerimas\Pages\CreatePenerima;
use App\Filament\Resources\Penerimas\Pages\EditPenerima;
use App\Filament\Resources\Penerimas\Pages\ListPenerimas;
use App\Filament\Resources\Penerimas\Pages\ViewPenerima;
use App\Filament\Resources\Penerimas\Schemas\PenerimaForm;
use App\Filament\Resources\Penerimas\Schemas\PenerimaInfolist;
use App\Filament\Resources\Penerimas\Tables\PenerimasTable;
use App\Models\Penerima;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenerimaResource extends Resource
{
    protected static ?string $model = Penerima::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return PenerimaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenerimaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenerimasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenerimas::route('/'),
            'create' => CreatePenerima::route('/create'),
            'view' => ViewPenerima::route('/{record}'),
            'edit' => EditPenerima::route('/{record}/edit'),
        ];
    }
}
