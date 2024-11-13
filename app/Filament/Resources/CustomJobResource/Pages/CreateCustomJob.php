<?php

namespace App\Filament\Resources\CustomJobResource\Pages;

use App\Filament\Resources\CustomJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomJob extends CreateRecord
{
    protected static string $resource = CustomJobResource::class;
}
