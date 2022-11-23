<?php

namespace App\Console\Commands;

use App\Services\MadelineProto\SyncService;
use Illuminate\Console\Command;

class SyncSingleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:sync {--path=} {--tg=}';

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
        $sync = new SyncService();
        if(!empty($this->option('tg')) && empty($this->option('path'))){
            $url = $this->option('tg');
            $sync->sync(null, $url);
            return;
        }
        if(empty($this->option('tg')) && !empty($this->option('path'))){
            $sync->sync($this->option('path'));
            return;
        }
    }
}
