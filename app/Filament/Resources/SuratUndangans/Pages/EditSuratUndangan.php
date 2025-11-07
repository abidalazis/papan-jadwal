<?php

namespace App\Filament\Resources\SuratUndangans\Pages;

use App\Filament\Resources\SuratUndangans\SuratUndanganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratUndangan extends EditRecord
{
    protected static string $resource = SuratUndanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
