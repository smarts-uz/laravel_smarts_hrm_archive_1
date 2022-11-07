<?php

namespace App\Services\Envato\ZipVerifier;

use App\Services\MadelineProto\MTProtoService;

class VerifierService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function verifier($start, $end = null)
    {

        if ($end != null) {
            for ($i = $start; $i <= $end; $i++) {
                $line = 'https://t.me/c/' . substr(env("CHANNEL_ID"), 4) . '/' . $i;
                $message = $this->MTProto->MadelineProto->messages->getDiscussionMessage(['peer' => env("CHANNEL_ID"), 'msg_id' => $i]);
                $comments = $this->MTProto->getComments($line);
                foreach ($comments as $comment) {
                    if ($comment['media']) {
                        if ($comment['media']['document']) {
                            if ($comment['media']['document']['mime_type'] == "application\/zip") {
                                $message = $this->MTProto->MadelineProto->channels->getMessages(["channel" => env("CHANNEL_ID"), "id" => [$i]]);
                                $Updates = $this->MTProto->MadelineProto->messages->editMessage([
                                    'peer'=>env("CHANNEL_ID"),'id'=>$i, 'message'=>str_replace("#New", "",$message['message'])]);
                            }
                        }
                    }

                }
            }
        }
    }
}
