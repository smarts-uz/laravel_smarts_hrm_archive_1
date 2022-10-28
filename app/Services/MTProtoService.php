<?php

namespace App\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

class MTProtoService
{
    public $MadelineProto;

    public function __construct()
    {
        $settings = new Settings;
        $settings->setAppInfo((new AppInfo)->setApiHash('0cc5751f00631d78d4dc5618864102dd')->setApiId(15108824));

        $this->MadelineProto = new API('/Users/ramziddinabdumominov/Documents/modelineProtoSession/session.madeline', $settings);
        $this->MadelineProto->start();
    }

    public function getComments($url)
    {
        $MTProto = new \App\Services\MTProtoService();
        $split = explode( "/", $url);
        $messages = $MTProto->MadelineProto->messages->getReplies(['peer' => -100 . $split[4], 'msg_id' => $split[5]]);

        return $messages['messages'];
    }

}
