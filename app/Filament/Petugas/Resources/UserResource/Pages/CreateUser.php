<?php

namespace App\Filament\Petugas\Resources\UserResource\Pages;

use App\Filament\Petugas\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
