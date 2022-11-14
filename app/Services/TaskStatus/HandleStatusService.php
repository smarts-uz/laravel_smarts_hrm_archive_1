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
                $this->stop();
                break;
            default:
                $this->messages->sendMessage(['peer' => 1307688882, 'message' => $message]);
        }
    }


}


