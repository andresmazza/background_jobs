<?php

namespace App\Filament\Resources\CustomJobResource\Pages;

use App\Filament\Resources\CustomJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomJobs extends ListRecords
{
    protected static string $resource = CustomJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
