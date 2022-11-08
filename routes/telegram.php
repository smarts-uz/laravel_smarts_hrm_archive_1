<?php

use App\Services\ManageService;
use SergiX44\Nutgram\Nutgram;




$config = [
    'timeout' => 60,
];

$servise = new ManageService();
$servise->handle($bot);
