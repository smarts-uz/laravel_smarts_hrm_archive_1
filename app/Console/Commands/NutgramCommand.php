<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;
use function PHPUnit\Framework\exactly;


class NutgramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nutgram:status';

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
        $bot = new Nutgram(env('TELEGRAM_TOKEN'));
        $pm = explode(',', setting('participants.pm'));

        $bot->onText("#TASK \n{text}", function (Nutgram $bot, $text) {
            file_put_contents('C:\Users\Pavilion\Documents\MadelineProto\JSONs\From_' . $bot->update()->message->from->id . '-' . $bot->update()->message->message_id . '.json', json_encode($bot->update()->message));
            print_r($bot->update()->message->from->id);
//    $txt = $bot->update()->message;

            $bot->editMessageText((string)$bot->update()->message->text . "\r\n\r\n#New", ['chat_id' => $bot->update()->message->forward_from_chat->id, 'message_id' => $bot->update()->message->forward_from_message_id]);
        });

        $bot->onText("#{text}", function (Nutgram $bot, $text) {


            switch (strtolower($text)) {
                case 'ok':
                case 'bug':
                    $qa = explode(', ', setting('participants.qa'));
                    if (in_array((string)$bot->update()->message->from->id, $qa)) {
                        break;
                    }
                    break;
                case 'ready':
                    $dev = explode(', ', setting('participants.dev'));
                    if (in_array((string)$bot->update()->message->from->id, $dev)) {
                        break;
                    }
                    break;
                case 'rejected':
                case 'accepted':
                $pm = explode(',', setting('participants.pm'));
                if (in_array((string)$bot->update()->message->from->id, $pm)) {
                    break;
                }
                    break;
                case 'inprogress':
                    $text2 = str_replace('New', 'inProgress', $bot->update()->message->reply_to_message->text);
                    $bot->editMessageText($text2, ['chat_id' => $bot->update()->message->reply_to_message->sender_chat->id, 'message_id' => $bot->update()->message->reply_to_message->forward_from_message_id]);
                break;
            }
        });

        $bot->run();
    }
}
