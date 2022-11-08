<?php

use App\Services\ManageService;

$config = [
    'timeout' => 60,
];

$servise = new ManageService();
$servise->handle($bot);

