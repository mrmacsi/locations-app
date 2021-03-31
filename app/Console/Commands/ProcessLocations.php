<?php

namespace App\Console\Commands;

use App\Services\Interfaces\LocationsServiceInterface;
use Illuminate\Console\Command;

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
     * @param  LocationsServiceInterface  $locationsService
     * @return int
     */
    public function handle(LocationsServiceInterface $locationsService)
    {
        $url = "https://parlvid.mysociety.org/os/ONSPD/2020-05.zip";
        $this->info('Downloading and extracting...');
        $csvFileName = $locationsService->downloadAndExtract($url);
        if (is_array($csvFileName)) {
            $this->info($csvFileName['error']);
            return ;
        }
        $this->info('CSV importing to DB...');
        $locationsService->insertToDB($csvFileName);
        $this->info('Completed');
    }
}
