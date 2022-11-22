<?php

namespace App\Console\Commands;

use App\Models\Dinner;
use App\Models\UserDinnerHistory;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dinner = new Dinner();
        print_r($dinner->get()->where('is_available', 1)->toArray());
        dd($dinner->get()->where('is_available', 1)->toArray());

    }
}
