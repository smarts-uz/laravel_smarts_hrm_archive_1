<?php

namespace App\Services\MadelineProto\Database;

use App\Services\MadelineProto\MTProtoService;

class DownloadMediaService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function folderPath($post, $path){
        $chat_id = !is_null($post->peer_id_user_id) ? $post->peer_id_user_id : $post->peer_id_channel_id;
        dump($post->id);
        dump($chat_id);
        dump($post->peer_id_user_id);
        dump($post->peer_id_channel_id);


        if (!is_dir($path . '/' . $chat_id)) {
            mkdir($path . '/' . $chat_id);
        }
        $path  .= '/' .$chat_id . '/';

        if (!is_dir($path . '/' . $post->tg_id)) {
            mkdir($path . '/' . $post->tg_id);
        }
        $path  .= '/' .$post->tg_id . '/';
        return $path;
    }

    public function downloadMedia($path, $post){

        $media = json_decode($post->media, true);
        if(array_key_exists('document', $media)){
            $attributes = [];
            foreach ($media['document']['attributes'] as $attribute){
                $attributes[] = $attribute['_'];
            }
            if(in_array('documentAttributeFilename', $attributes)){
                dump('documentAttributeFilename');
                $this->MTProto->MadelineProto->downloadToFile($media, $path .
                    $media['document']['attributes'][array_search('documentAttributeFilename',$attributes)]['file_name']);

            }else{
                dump('NO   documentAttributeFilename');
                $mime = explode('/',$media['document']['mime_type']);
                $this->MTProto->MadelineProto->downloadToFile($media, $path .
                    $media['document']['id'] . '.' . $mime[1]);
            }

        }

    }
}
