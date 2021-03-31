<?php

namespace App\Services;

use App\Models\Locations;
use App\Services\Interfaces\LocationsServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LocationsService implements LocationsServiceInterface
{
    public function getAll(int $limit = 1000): Collection
    {
        return Locations::select('postcode','latitude','longitude')
            ->take($limit)->get();
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

    public function insertToDB(): array
    {
        $csvFileName = "ONSPD_MAY_2020_UK_AB.csv";
        $csvFile = storage_path($csvFileName);
        $read = $this->readCSV($csvFile, array('delimiter' => ','));
        $postcodeColumn = 0;
        $latColumn = 0;
        $longColumn = 0;
        foreach ($read as $key => $item) {
            if ($key == 0) {
                $postcodeColumn = array_search("pcd", $item);
                $latColumn = array_search("lat", $item);
                $longColumn = array_search("long", $item);
            } else {
                $postCode = str_replace(" ", "", $item[$postcodeColumn]);
                $lat = $item[$latColumn];
                $long = $item[$longColumn];
                if ($postCode && $lat && $long) {
                    $locations = new Locations();
                    $locations->postcode = $postCode;
                    $locations->latitude = $lat;
                    $locations->longitude = $long;
                    $locations->save();
                }
            }
        }
        return $read;
    }

    public function readCSV($csvFile, $array): array
    {
        $file_handle = fopen($csvFile, 'r');
        $line_of_text = [];
        while ( !feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
