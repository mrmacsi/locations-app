<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface LocationsServiceInterface
{
    public function searchNearBy($latitude, $longitude): Collection;

    public function getAll(): Collection;
}
