<?php

use App\Services\ManageService;


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . env('DROPPER_BOT_TOKEN') . "/getWebhookInfo");

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
