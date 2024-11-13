<?php

namespace App\Filament\Resources\CustomJobResource\Pages;

use App\Filament\Resources\CustomJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomJob extends EditRecord
{
    protected static string $resource = CustomJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
