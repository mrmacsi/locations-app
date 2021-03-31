<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Services\Interfaces\LocationsServiceInterface;

class LocationsController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getAllLocations(LocationsServiceInterface $locationsService)
    {
        $all = $locationsService->getAll(1000);
        return view('all',['all'=>$all]);
    }

    public function searchBy(LocationRequest $request, LocationsServiceInterface $locationsService)
    {
        try {
            $request->validated();
            $latitude = $request->query('lat');
            $longitude = $request->query('long');
            $search = $locationsService->searchNearBy($latitude, $longitude);
            return view('search',['all'=>$search]);
        }catch (\Exception $e){
            return redirect()->back();
        }
    }
}
