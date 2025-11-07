<?php

namespace App\Filament\Resources\Penerimas\Pages;

use App\Filament\Resources\Penerimas\PenerimaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPenerima extends EditRecord
{
    protected static string $resource = PenerimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
