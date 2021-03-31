<?php

namespace App\Services;

use App\Models\Locations;
use App\Services\Interfaces\LocationsServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LocationsService implements LocationsServiceInterface
{
    public function getAll(): Collection
    {
        return Locations::select('postcode')->pluck('postcode');
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public function searchNearBy($latitude, $longitude): Collection
    {
        $radius = 10;
        return Locations::select(
            DB::raw("postcode, latitude, longitude, (
            3959 * acos(
                cos( radians(  ?  ) ) *
                cos( radians( latitude ) ) *
                cos( radians( longitude ) - radians(?) ) +
                sin( radians(  ?  ) ) *
                sin( radians( latitude ) )
            )
       ) AS distance")
        )
            ->having("distance", "<", "?")
            ->orderBy("distance")
            ->take(20)
            ->setBindings([$latitude, $longitude, $latitude, $radius])
            ->get();
    }
}
