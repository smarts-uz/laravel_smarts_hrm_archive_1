<?php


namespace App\Services\TaskStatus;


use danog\MadelineProto\EventHandler;


class HandleStatusService extends EventHandler
{


    public function onUpdateNewMessage(array $update)
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }

        $qa = explode(', ', setting('participants.qa'));
        $dev = explode(', ', setting('participants.dev'));
        $pm = explode(',', setting('participants.pm'));
        $message = $update['message']['message'];

        switch (strtolower($message)) {
            case '#bug':
                if (in_array($update['message']['from_id']['user_id'], $qa)) {
                    $disscussion = $this->messages->getDiscussionMessage(['peer' => '-100' . $update['message']['peer_id']['channel_id'], 'msg_id' => $update['message']['reply_to']['message_id']]);
                    print_r($disscussion);
                    /*$this->messages->editMessage(['peer' => '-100' . $update['message']['peer_id']['channel_id'], 'id' => $update['message']['reply_to']['message_id'],
                        'message' =>]);*/
                } else {
                    $this->messages->sendMessage(['peer' =>'-100' . $update['message']['peer_id']['channel_id'],
                        'message' => 'User ID: ' . $update['message']['from_id']['user_id'] . ' is not a member of QA Team.']);
                }
        }

        $path = 'C:\Users\Pavilion\Documents\MadelineProto\JSONs\Updates/';
        if (str_starts_with((string)$message, 'stopmadeline')) {
            $this->stop();
        } else if (str_starts_with((string)$message, 'comment')) {
            file_put_contents($path . '/F ' . $update['message']['from_id']['user_id'] . '-' . $update['message']['id'] . ' Mes: ' . $message . '.json', json_encode($update));
//            $discussion = yield $this->messages->getDiscussionMessage(['msg_id'=> $update['message']['reply_to']["reply_to_msg_id"]]);
//            print_r($discussion);
            $this->messages->sendMessage(['peer' => 1244414566, 'message' => json_encode($update)]);
        }
    }

    public function onUpdateNewChannelMessage(array $update)
    {
        return $this->onUpdateNewMessage($update);
    }
}


