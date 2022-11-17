<?php

namespace App\Console\Commands;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
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

    public $MadelineProto;

    public function handle()
    {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
        $dev = User::where('role_id', '=', env('DEVELOPER_ID'))->pluck('telegram_id')->all();
        $pm = User::where('role_id', '=', env('PM_ID'))->pluck('telegram_id')->all();
        $qa = User::where('role_id', '=', env('QA_ID'))->pluck('telegram_id')->all();
        $this->status = [
            '#Bug' => ['id' => $qa, 'tag' => '#ActiveBug'],
            '#OK' => ['id' => $qa, 'tag' => '#Completed'],
            '#Ready' => ['id' => $dev, 'tag' => '#NeedTests'],
            '#Reject' => ['id' => $pm, 'tag' => '#Rejected'],
            '#Accept' => ['id' => $pm, 'tag' => '#Accepted'],
        ];
        while (true) {
            $end = $this->MadelineProto->messages->getHistory([
            'peer' => -1001852006251, 'limit' => 1,])['messages'][0]['id'];
            $this->getPostId('1', $end);
        }
    }

    public $status;

    public function getPostId($start, $end)
    {
        $channel_id = -100 . env('STATUS_CHANNEL_ID');

        for ($i = $start; $i <= $end; $i++) {

            $item = $this->MadelineProto->channels->getMessages([
                "channel" => $channel_id,
                "id" => [$i]])['messages'];
            if (array_key_exists('message', $item[0])) {
                file_put_contents('s.json', json_encode($item));
                $this->getComments(
                    $channel_id, $item[0]['id'],
                    $item[0]['replies']['replies'],
                    $item[0]['message']);
            }
            sleep(1);
        }
        sleep(60);
    }

    protected function getComments($channel_id, $id, $replies, $message)
    {
        switch (true) {
            case $replies > 0:
                $comments = $this->MadelineProto->messages->getReplies(['peer' => $channel_id, 'msg_id' => $id]);
                $this->confirmStatus($comments['messages'][0], $message, $id);
                break;
            default:
                $this->addTags($message, $id, '/\s#(\w+)/', '#ActiveTask');
                break;
        }
    }

    public function confirmStatus($comment, $message, $id)
    {
        $tag = true;
        foreach ($this->status as $key => $item) {
            if ($comment['message'] === $key &&
                (array_key_exists('from_id', $comment) &&
                    in_array((string)$comment['from_id']['user_id'], $item['id']))) {
                $this->addTags($message, $id, '/\s#(\w+)/', $item['tag']);
                $tag = false;
            }
        }
        if ($tag) {
            $this->addTags($message, $id, '/\s#(\w+)/', '#InProcess');
        }
    }

    protected function addTags($message, $id, $pattern, $tag)
    {
        $newMessage = preg_replace($pattern, '', $message);
        $newMessage .= '
' . $tag;
        if ($newMessage !== $message) {
            $this->MadelineProto->messages->editMessage(
                ['peer' => -100 . env('STATUS_CHANNEL_ID'),
                    'id' => $id,
                    'message' => $newMessage]);
        }
    }
}
