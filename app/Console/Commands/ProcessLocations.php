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
        $locationsService->insertToDB();
    }
}
