<?php

namespace App\Console\Commands;

use App\Services\TaskStatus\EditStatusService;
use Illuminate\Console\Command;
use App\Services\TaskStatus\TaskListService;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Logger;
use TCG\Voyager\Models\User;

class TaskList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taskList:run';

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
        $settings = (new \danog\MadelineProto\Settings\Database\Mysql)
            ->setUri('tcp://localhost')
            ->setPassword('');
        $settings->getLogger()->setLevel(Logger::LEVEL_ULTRA_VERBOSE);
        TaskListService::startAndLoop(env('SESSION_PUT'), $settings);
    }
}
