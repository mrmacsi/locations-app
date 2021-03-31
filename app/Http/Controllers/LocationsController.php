<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\LocationsServiceInterface;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function getAllLocations(LocationsServiceInterface $locationsService)
    {
        return $locationsService->getAll();
    }

    public function searchBy(Request $request, LocationsServiceInterface $locationsService)
    {
        $latitude = $request->query('lat');
        $longitude = $request->query('long');
        return $locationsService->searchNearBy($latitude, $longitude);
    }
}
