<?php

use App\Services\ManageService;
use SergiX44\Nutgram\Nutgram;


$config = [
    'timeout' => 60,
];

$bot  = new Nutgram(env('MANAGER_BOT_TOKEN'), $config);
$servise = new ManageService();
$servise->handle($bot);
