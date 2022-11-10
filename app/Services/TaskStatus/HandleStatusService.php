<?php


namespace App\Services\TaskStatus;


use App\Services\MadelineProto\MTProtoService;
use danog\MadelineProto\EventHandler;


class HandleStatusService extends EventHandler
{
    public $MTProto;

    public function onUpdateNewMessage(array $update): \Generator
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }

        $message = $update['message']['message'];

        switch (strtolower((string)$message)) {
            case 'hello':
                yield $this->messages->sendMessage(['peer' => $update, 'message' => "Hi!", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
                break;
            case 'salom':
                yield $this->messages->sendMessage(['peer' => $update, 'message' => "Salom!", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
                break;
            case 'ishla yaxshimi':
                yield $this->messages->sendMessage(['peer' => $update, 'message' => "Bo'vottimi", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
                break;
            default:
                yield $this->messages->sendMessage(['peer' => $update, 'message' => (string)$message, 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
                break;
        }
    }

}
