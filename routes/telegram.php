<?php

use SergiX44\Nutgram\Nutgram;


$bot->onText("#TASK \n{text}", function (Nutgram $bot, $text) {
    file_put_contents('C:\Users\Pavilion\Documents\MadelineProto\JSONs\From_' . $bot->update()->message->from->id . '-' . $bot->update()->message->message_id . '.json', json_encode($bot->update()->message));
    print_r($bot->update()->message->from->id);
//    $txt = $bot->update()->message;

    $bot->editMessageText((string)$bot->update()->message->text . "\r\n\r\n#New", ['chat_id' => $bot->update()->message->forward_from_chat->id, 'message_id' => $bot->update()->message->forward_from_message_id]);
});

$bot->onText("#{text}", function (Nutgram $bot, $text) {


    switch ($text) {
        case 'inProgress':
            file_put_contents('C:\Users\Pavilion\Documents\MadelineProto\JSONs\From_' . $bot->update()->message->from->id . '-' . $bot->update()->message->message_id . '.json', json_encode($bot->update()->message));
            $text2 = str_replace('New', 'inProgress', $bot->update()->message->reply_to_message->text);
            $bot->editMessageText($text2, ['chat_id' => $bot->update()->message->reply_to_message->sender_chat->id, 'message_id' => $bot->update()->message->reply_to_message->forward_from_message_id]);

    }
});

$bot->run();
/*
use App\Services\ManageService;


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".env('MANAGER_BOT_TOKEN')."/getWebhookInfo");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$output = curl_exec($ch);
$output = json_decode($output);
if ($output->result->url === '') {

    $config = [
        'timeout' => 60,
    ];

    $servise = new ManageService();
    $servise->handle($bot);
}

curl_close($ch);
*/


