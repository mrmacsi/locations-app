<?php

namespace App\Services;

use App\Models\Locations;
use App\Services\Interfaces\LocationsServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

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

    public function insertToDB($csvFileName): array
    {
        try {
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
        } catch (\Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public function downloadAndExtract($url){
        try {
            $guzzle = new Client();
            $response = $guzzle->get($url);
            Storage::put('csvfile.zip', $response->getBody());
            $Path = storage_path('app/csvfile.zip');
            $is_valid = Zip::check($Path);
            if ($is_valid) {
                $zip = Zip::open($Path);
                $list = $zip->listFiles();
                $csvFileName = "";
                //get the first found csv for now it takes too long to insert to db
                foreach($list as $item) {
                    if(strpos($item,'.csv') !== false) {
                        $csvFileName = $item;
                        break;
                    }
                }
                $zip->extract(storage_path(),$csvFileName);
                $zip->close();
                return $csvFileName;
            }else{
                return ['error' => 'Not valid'];
            }
        } catch (\Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public function readCSV($csvFile, $array): array
    {
        try {
            $file_handle = fopen($csvFile, 'r');
            $line_of_text = [];
            while ( !feof($file_handle)) {
                $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
            }
            fclose($file_handle);
            return $line_of_text;
        } catch (\Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}
