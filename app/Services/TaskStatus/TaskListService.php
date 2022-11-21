<?php

namespace App\Services\TaskStatus;
use danog\MadelineProto\API;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Tools;
use Illuminate\Support\Facades\DB;

use danog\MadelineProto\RPCErrorException;

class TaskListService extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "@firefox109"; // Change this

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

    public function onUpdateChannelMessageForwards(array $update): \Generator
    {
        return $this->updateChannelMessageForwards($update);
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
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $res = \json_encode($update, JSON_PRETTY_PRINT);
        /*$post = DB::table('tmp_212_mtproto_chats')->where('key', -1001711427913)->first();
        $a = $post;*/
        yield $this->messages->sendMessage(['peer' => $update, 'message' => "<code>$res</code>", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
            yield $this->messages->sendMedia(['peer' => $update, 'message' => $update['message']['message'], 'media' => $update]);
        }

        // You can also use the built-in MadelineProto MySQL async driver!

        // Can be anything serializable, an array, an int, an object
        $myData = [];

        // Use the isset method to check whether some data exists in the database
        if (yield $this->dataStoredOnDb->isset('yourKey')) {
            // Always yield when fetching data
            $myData = yield $this->dataStoredOnDb['yourKey'];
        }
        $this->dataStoredOnDb['yourKey'] = $myData + ['moreStuff' => 'yay'];

        $this->dataStoredOnDb['otherKey'] = 0;
        unset($this->dataStoredOnDb['otherKey']);

        $this->logger("Count: ".(yield $this->dataStoredOnDb->count()));

        // You can even use an async iterator to iterate over the data
        $iterator = $this->dataStoredOnDb->getIterator();
        while (yield $iterator->advance()) {
            [$key, $value] = $iterator->getCurrent();
            $this->logger($key);
            $this->logger($value);
        }
    }
}
