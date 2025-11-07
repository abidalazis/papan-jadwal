<?php

namespace App\Filament\Resources\SuratUndangans\Pages;

use App\Filament\Resources\SuratUndangans\SuratUndanganResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSuratUndangan extends ViewRecord
{
    protected static string $resource = SuratUndanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
