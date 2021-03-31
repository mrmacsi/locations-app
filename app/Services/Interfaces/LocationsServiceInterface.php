<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface LocationsServiceInterface
{
    public function searchNearBy($latitude, $longitude): Collection;

    public function getAll(int $limit): Collection;

    public function insertToDB(): array;

    public function readCSV($csvFile, $array): array;
}
