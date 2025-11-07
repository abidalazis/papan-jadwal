<?php

namespace App\Filament\Resources\Penerimas\Pages;

use App\Filament\Resources\Penerimas\PenerimaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPenerima extends ViewRecord
{
    protected static string $resource = PenerimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
