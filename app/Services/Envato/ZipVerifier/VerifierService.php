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

    public function verifier($start, $end)
    {
        for ($i = $start; $i <= $end; $i++) {
            $line = 'https://t.me/c/' . substr(env("CHANNEL_ID"), 4) . '/' . $i;
            try {
                $message = $this->MTProto->MadelineProto->channels->getMessages(["channel" => env("CHANNEL_ID"), "id" => [$i]]);
                file_put_contents('/Users/ramziddinabdumominov/Documents/Json/ZipVerifier/' . $i . '.json', json_encode($message));
                $comments = $this->MTProto->getComments($line);
                if (count($comments) === 0) {
                    if (!str_contains($message['messages'][0]['message'], "#New")) {
                        $Updates = $this->MTProto->MadelineProto->messages->editMessage([
                            'peer' => env("CHANNEL_ID"), 'id' => $i, 'message' => $message['messages'][0]['message'] . "\r\n\r\n#New"]);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
            foreach ($comments as $comment) {
                try {
                    if ($comment['media']) {

                        if ($comment['media']['document']) {
                            if ($comment['media']['document']['mime_type'] == "application\/zip") {
                                if (str_contains($message['messages'][0]['message'], "#New")) {
                                    print_r("Has #New !");
                                    print_r(PHP_EOL);
                                    print_r($message['messages'][0]['message']);
                                    print_r(PHP_EOL);
                                    print_r(str_replace("#New", "", $message['messages'][0]['message']));
                                    print_r(PHP_EOL);
                                    print_r(PHP_EOL);
                                    $this->MTProto->MadelineProto->messages->editMessage([
                                        'peer' => env("CHANNEL_ID"), 'id' => $i, 'message' => str_replace("#New", "", $message['messages'][0]['message'])]);
                                }
                            } else {
                                if (!str_contains($message['messages'][0]['message'], "#New")) {
                                    print_r("Has #New !");
                                    print_r(PHP_EOL);
                                    print_r($message['messages'][0]['message']);
                                    print_r(PHP_EOL);
                                    print_r($message['messages'][0]['message'] . "\r\n\r\n#New");
                                    print_r(PHP_EOL);
                                    print_r(PHP_EOL);
                                    $Updates = $this->MTProto->MadelineProto->messages->editMessage([
                                        'peer' => env("CHANNEL_ID"), 'id' => $i, 'message' => $message['messages'][0]['message'] . "\r\n\r\n#New"]);
                                }
                            }
                        }
                    }

                } catch
                (\Exception $e) {
                    continue;
                }
            }
        }
    }
}

