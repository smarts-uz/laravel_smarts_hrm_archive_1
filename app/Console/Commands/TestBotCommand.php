<?php

namespace App\Console\Commands;

use App\Services\TestBot;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Illuminate\Console\Command;

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
        $end = $this->MadelineProto->messages->getHistory(['peer' => -1001852006251, 'limit' => 1,])['messages'][0]['id'];
        $this->getPostId('1', $end);
    }

    public function getPostId($start, $end) {

        $channel_id = -1001852006251;

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
    }

    protected function getComments($channel_id, $id, $replies, $message) {
        switch(true)
        {
            case $replies > 0:
                $comments = $this->MadelineProto->messages->getReplies(['peer' => -1001852006251, 'msg_id'=> $id]);
                return $comments[0];
                break;
            default:
            echo 'bla bla';
                break;
        }
    }

    protected function addTags($message, $id) {
        $newMessage = str_replace(['#New', '   ' . '#New'], ['' , ''], $message);
        if ($newMessage !== $message) {
            $newMessage  = $newMessage . " " . '#New';
            $this->MadelineProto->messages->editMessage(
                ['peer'   => -100 .env('CHANNEL_ID'),
                    'id'      => $id,
                    'message' => $newMessage]);
        }
    }

    protected function removeTags($message, $id) {
        $newMessage = str_replace(['#New', '   ' . '#New'], ['' , ''], $message);
        if ($newMessage !== $message) {
            $this->MadelineProto->messages->editMessage(
                ['peer'   => -100 .env('CHANNEL_ID'),
                    'id'      => $id,
                    'message' => $newMessage]);
        }
    }
}
