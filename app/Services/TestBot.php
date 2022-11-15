<?php

namespace App\Services;


use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Tools;
use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings;
use danog\MadelineProto\RPCErrorException;

/**
 * Event handler class.
 */
class TestBot extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "smartSoftware_bot"; // Change this

    /**
     * List of properties automatically stored in database (MySQL, Postgres, redis or memory).
     * @see https://docs.madelineproto.xyz/docs/DATABASE.html
     * @var array
     */

    public $MadelineProto;

    public function getmes () {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
        $post_id = $this->MadelineProto->messages->getDiscussionMessage([
            'peer' => -1001711427913, 'msg_id' => 45]);
        $post =$this->MadelineProto->messages->getMessages([
            "channel" => -1001711427913,
            "id" => 42]);
        return $post_id;
    }

    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array'
    ];

    /**
     * @var DbArray<array>
     */
    protected $dataStoredOnDb;

    /**
     * Get peer(s) where to report errors
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return [self::ADMIN];
    }
    /**
     * Called on startup, can contain async calls for initialization of the bot
     */
    public function onStart()
    {
    }
    /**
     * Handle updates from supergroups and channels
     *
     * @param array $update Update
     */
    public function onUpdateNewChannelMessage(array $update)
    {
        return $this->onUpdateNewMessage($update);

    }
    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update)
    {

        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        //$a = $this->getmes();
        file_put_contents('dis.json', json_encode($update));
        $this->messages->sendMessage(['peer' => $update,
            'message' => $update['message']['message'],
            'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null]);
        //file_put_contents('s.json', json_encode($a));
        /*$post_id = $this->MadelineProto->messages->getDiscussionMessage([
            'peer' => -1001711427913, 'msg_id' => 41])['messages'][0]['id'];*/

        if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
            $this->messages->sendMedia(['peer' => $update,
            'message' => $update['message']['message'],
            'media' => $update]);
        }
    }
}

