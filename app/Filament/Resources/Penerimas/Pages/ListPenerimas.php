<?php

namespace App\Filament\Resources\Penerimas\Pages;

use App\Filament\Resources\Penerimas\PenerimaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenerimas extends ListRecords
{
    protected static string $resource = PenerimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
