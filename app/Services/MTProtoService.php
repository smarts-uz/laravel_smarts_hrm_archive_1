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
        $settings->setAppInfo((new AppInfo)->setApiHash('adcaaf6ff60778f454ee90f3a6c26c7b')->setApiId(9330195));

        $this->MadelineProto = new API('D:/session.madeline', $settings);
        $this->MadelineProto->start();
    }

}
