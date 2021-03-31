<?php

namespace App\Console\Commands;

use App\Models\Locations;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessLocations extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'process:locations';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        /*$csvFileName = "ONSPD_MAY_2020_UK_AB.csv";
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
        return $read;*/
        $long = '-2.133104';
        $lat = '57.129226';
        $property =
            Locations::select(
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
                ->setBindings([$lat, $long, $lat,  15])
                ->get();
        dd($property);
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while ( !feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }
}
