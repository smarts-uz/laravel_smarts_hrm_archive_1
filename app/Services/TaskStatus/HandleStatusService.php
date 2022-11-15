<?php


namespace App\Services\TaskStatus;


use App\Services\MadelineProto\MTProtoService;
use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;


class HandleStatusService extends EventHandler
{

    public $Madeline;

    public function onUpdateNewMessage(array $update)
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $res = \json_encode($update, JSON_PRETTY_PRINT);

        echo gettype($update."\n");
        file_put_contents('update.json', json_encode($update, JSON_THROW_ON_ERROR));
        $mes = $update['message'];
        print_r($mes."\n");
        $user = $mes['from_id']['user_id'];
        print_r($user."\n");
        $message = $mes['message'];
        print_r($message."\n");

        switch ((string)$message) {
            case 'stop madeline':
                $this->Madeline->MadelineProto->stop();
                break;
            case 'start madeline':
                $this->startAndLoop(env('SESSION_PUT') . '/session.madeline', $this->Madeline->settings);
                break;
            default:
                $this->messages->sendMessage(['peer' => 1307688882, 'message' => $message]);
        }

    }

    public function __construct($API)
    {
        parent::__construct($API);
        $this->Madeline = new MTProtoService();
    }

}


