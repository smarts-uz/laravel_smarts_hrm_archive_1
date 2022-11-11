<?php

namespace App\Services\Envato\ZipVerifier;

use App\Services\Envato\EnvatoService;
use App\Services\MadelineProto\MTProtoService;
use danog\MadelineProto\API;

class VerifierService
{
    use EnvatoService;

    protected function getComments($channel_id, $id, $replies, $url, $message) {
        switch(true)
        {
            case $replies > 0:
                $comments = $this->MadelineProto->messages->getReplies(['peer' => -100 .$channel_id, 'msg_id'=> $id]);
                $this->sortMessage($message, $comments['messages'], $id);
                break;
            default: $post_id = $this->MadelineProto->messages->getDiscussionMessage([
                'peer' => -100 . $channel_id, 'msg_id' => $id])['messages'][0]['id'];
                $this->addTags($message, $id);
                break;
        }
    }

    public function sortMessage($message, $comments, $id) {
        $a = true;
        foreach ($comments as $comment) {
            if (array_key_exists('media', $comment)
            && array_key_exists('document', $comment['media'])
            && array_key_exists('mime_type', $comment['media']['document'])
            && preg_match('#(zip)#',
                    $comment['media']['document']['mime_type'], $arr)) {
                $a = false;
            }
        }
        if ($a) {
            $this->addTags($message, $id);
        } else {
            $this->removeTags($message, $id);
        }
    }

    protected function addTags($message, $id) {
        $newMessage = str_replace(['#New', '   ' . '#New'], ['' , ''], $message);
        if ($newMessage !== $message) {
            $newMessage  = $newMessage . " " . '#New';
            $this->MadelineProto->messages->editMessage(
                ['peer'   => -100 .env('CHANNEL_ID'),
                    'id'      => $id,
                    'message' => $newMessage]);
        }
    }

    protected function removeTags($message, $id) {
        $newMessage = str_replace(['#New', '   ' . '#New'], ['' , ''], $message);
        if ($newMessage !== $message) {
            $this->MadelineProto->messages->editMessage(
                ['peer'   => -100 .env('CHANNEL_ID'),
                    'id'      => $id,
                    'message' => $newMessage]);
        }
    }
}
