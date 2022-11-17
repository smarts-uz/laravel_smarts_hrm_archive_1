<?php
//
//use App\Services\ManageService;
//
//
//$ch = curl_init();
//
//curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".env('DROPPER_BOT_TOKEN')."/getWebhookInfo");
//
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//$output = curl_exec($ch);
//$output = json_decode($output);
//if ($output->result->url === '') {
//
//    $config = [
//        'timeout' => 60,
//    ];
//
//    $servise = new ManageService();
//    $servise->handle($bot);
//}
//
//curl_close($ch);
//
///*use SergiX44\Nutgram\Conversations\Conversation;
//use SergiX44\Nutgram\Nutgram;
//
//class MyConversation extends Conversation {
//
//    public function start(Nutgram $bot)
//    {
//        $bot->sendMessage('This is the first step!');
//        $this->next('secondStep');
//    }
//
//    public function secondStep(Nutgram $bot)
//    {
//        $bot->sendMessage('Bye!');
//        $this->end();
//    }
//}
//
//
//$bot->onCommand('start', MyConversation::class);
//$bot->onText('{text}', function (Nutgram $bot, $text){
//    echo $text."\n";
//});
//
//$bot->run();*/
//
//
//
