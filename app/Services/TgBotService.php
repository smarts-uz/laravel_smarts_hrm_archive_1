<?php

namespace App\Services;


class TgBotService
{
    private $storage_path;

    public function __construct(){
        if (getenv('COMPUTERNAME') === "UmidAdilovAbdsattarovich") {
            $this->storage_path = env('ROOT_PATH');
        }
        else {
            $this->storage_path = env('LOCAL_PATH');
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}
