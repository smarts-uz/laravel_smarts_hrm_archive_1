<?php


namespace App\Services\TaskStatus;


use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;


class HandleStatusService extends EventHandler
{
    public function onUpdateNewMessage(array $update)
    {
        $mes = $update['message'];
        $user = $mes['from_id']['user_id'];
        $message = $mes['message'];

        switch ((string)$message) {
            default:
                $this->messages->sendMessage(['peer' => $user, 'message' => $message]);
        }
    }

    public function __construct()
    {
        $settings = new Settings;

        $settings->setAppInfo((new AppInfo)->setApiId(9330195)->setApiHash('adcaaf6ff60778f454ee90f3a6c26c7b'));
        $madelineproto = new API(env('SESSION_PUT') . '/session.madeline', $settings);
        $madelineproto->start();

        HandleStatusService::startAndLoop(env('SESSION_PUT') . '/bot.madeline', $settings);
    }
}


