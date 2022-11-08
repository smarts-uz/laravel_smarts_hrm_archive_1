<?php

use App\Services\ManageService;
use SergiX44\Nutgram\Nutgram;




$config = [
    'timeout' => 60,
];

$bot  = new Nutgram(env('MANAGER_BOT_TOKEN'), $config);
$servise = new ManageService();

$bot->onCommand('start', function (Nutgram $bot) {
    $bot->sendMessage('Tekshirmoqchi bo\'lgan useringizni idsini kiriting');
});

$bot->onText('id {user_id}', function (Nutgram $bot, $user_id, $servise) {
    if (is_numeric($user_id)) {
        $servise->user_id = $user_id;
        $user = $servise->getUser($bot, $user_id);
        $servise->Addbutton($user);
    } else {
        $bot->sendMessage('bu user idsi emas, id son bo\'lishi kerak');
    }
});

$bot->onText('Channels ❌', function (Nutgram $bot, $servise) {
    if ($servise->user_id !== null){
        $user_id = $servise->user_id;
        $servise->delFromChannel($bot,$user_id);
    }else{
        $bot->sendMessage('The first enter user id, please!');
    }
});

$bot->onText('Groups ❌', function (Nutgram $bot, $servise) {
    if ($servise->user_id !== null){
        $user_id = $servise->user_id;
        $servise->delFromGroup($bot, $user_id);
    }else{
        $bot->sendMessage('The first enter user id, please!');
    }
});

$bot->onText('All ❌', function (Nutgram $bot, $servise) {
    if ($servise->user_id !== null){
        $user_id = $servise->user_id;
        $servise->delFromChannel($bot,$user_id);
        $servise->delFromGroup($bot, $user_id);
    }else{
        $bot->sendMessage('The first enter user id, please!');
    }
});
