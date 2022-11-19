<?php

namespace App\Console\Commands\__;

use App\Services\TaskStatus\EditStatusService;
use Illuminate\Console\Command;
use TCG\Voyager\Models\User;

class TestBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testBot:run';

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

    public $status;

    public function handle(EditStatusService $status)
    {
        $this->status = $status;
        $dev = User::where('role_id', '=', env('DEVELOPER_ID'))->pluck('telegram_id')->all();
        $pm = User::where('role_id', '=', env('PM_ID'))->pluck('telegram_id')->all();
        $qa = User::where('role_id', '=', env('QA_ID'))->pluck('telegram_id')->all();
        $this->status->status = [
            '#OK' => ['id' => $qa, 'tag' => '#Completed'],
            '#Ready' => ['id' => $dev, 'tag' => '#NeedTests'],
            '#Reject' => ['id' => $pm, 'tag' => '#Rejected'],
            '#Accept' => ['id' => $pm, 'tag' => '#Accepted']
        ];
        $end = $this->status->MadelineProto->messages->getHistory([
            'peer' => -1001852006251, 'limit' => 1])['messages'][0]['id'];
        //$this->status->getPostId('1', $end);
        while(true) {
            $this->status->getHistory();
            sleep(2);
        }
    }
}
