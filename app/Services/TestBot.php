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
    public function onUpdateNewChannelMessage(array $update): \Generator
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
    public function onUpdateNewMessage(array $update): \Generator
    {
        file_put_contents('s.json', json_encode($update));
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $a = $this->messages->getDiscussionMessage([
        'peer' => '-100' . '1711427913',
        'msg_id' => 17
        ]);
        file_put_contents('dis.json', json_encode($update));
        yield $this->messages->sendMessage(['peer' => $update,
        'message' => $update['message']['message'],
        'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null]);
        if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
            yield $this->messages->sendMedia(['peer' => $update,
            'message' => $update['message']['message'],
            'media' => $update]);
        }
    }
}

