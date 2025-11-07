<?php

namespace App\Filament\Resources\SuratUndangans\Pages;

use App\Filament\Resources\SuratUndangans\SuratUndanganResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Actions;
use Carbon\Carbon;

class ListSuratUndangans extends ListRecords
{
    protected static string $resource = SuratUndanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    
}
