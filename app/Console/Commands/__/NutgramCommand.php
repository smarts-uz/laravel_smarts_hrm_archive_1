<?php

namespace App\Console\Commands\__;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;


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

        $bot->onText("#TASK \n{text}", function (Nutgram $bot, $text) {
            file_put_contents('C:\Users\Pavilion\Documents\MadelineProto\JSONs\From_' . $bot->update()->message->from->id . '-' . $bot->update()->message->message_id . '.json', json_encode($bot->update()->message));
            print_r($bot->update()->message->from->id);
//    $txt = $bot->update()->message;

            $bot->editMessageText((string)$bot->update()->message->text . "\r\n\r\n#New", ['chat_id' => $bot->update()->message->forward_from_chat->id, 'message_id' => $bot->update()->message->forward_from_message_id]);
        });

        $bot->onText("#{text}", function (Nutgram $bot, $text) {


            switch (strtolower($text)) {
                case 'ok':
                    if (in_array((string)$bot->update()->message->from->id, $qa)) {
                        $bot->editMessageText((string)$bot->update()->message->text . "\r\n\r\n#Completed", ['chat_id' => $bot->update()->message->reply_to_message->sender_chat->id, 'message_id' => $bot->update()->message->forward_from_message_id]);
                    } else {
                        $bot->sendMessage('"' . $bot->update()->message->from->first_name . '" is not a member of QA Team.', ['reply_to_message_id' => $bot->update()->message->reply_to_message->message_id]);
                    }
                    break;
                case 'bug':
                    $qa = explode(', ', setting('participants.qa'));
                    print_r($qa);
                    if (in_array((string)$bot->update()->message->from->id, $qa)) {
                        $bot->editMessageText((string)$bot->update()->message->text . "\r\n\r\n#InProcess", ['chat_id' => $bot->update()->message->reply_to_message->sender_chat->id, 'message_id' => $bot->update()->message->forward_from_message_id]);
                    } else {
                        $bot->sendMessage('"' . $bot->update()->message->from->first_name . '" is not a member of QA Team.', ['reply_to_message_id' => $bot->update()->message->reply_to_message->message_id]);
                    }
                    break;
                case 'ready':
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
