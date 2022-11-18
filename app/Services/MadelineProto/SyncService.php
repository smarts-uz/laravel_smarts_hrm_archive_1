<?php

namespace App\Services\MadelineProto;

use App\Services\FileSystemService;

class SyncService
{
    public $MTProto;
    public function __construct()
    {
        $this->MTProto = new FileSystemService();
    }

    public function sync($path)
    {
        $file_system = new FileSystemService();
        $MTProto = new MTProtoService();
        $url_file = $file_system->searchForUrl($path);
        if($url_file == null){
        return;
        }
        print_r($url_file);
        $url = $file_system->readUrl($url_file);
        $comments = $MTProto->getComments($url);
        $tg_files = $MTProto->getFiles($comments);
        $storage_files = $file_system->getFIles($path);
        $to_tg = array_diff($storage_files, $tg_files);
        $to_st = array_diff($tg_files, $storage_files);
        print_r($comments);
        $split = explode("/", $url);
        $message = $MTProto->MadelineProto->messages->getDiscussionMessage(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]]);
        foreach ($to_tg as $item) {
            $descr = $file_system->caption($path . '/' . $item);
            $MTProto->MadelineProto->messages->sendMedia(["peer" => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                "reply_to_msg_id" => (int)$message['messages'][0]['id'], "media" => ['_' => 'inputMediaUploadedDocument',
                    'file' => $path . '/' . $item, 'attributes' => [
                        ['_' => 'documentAttributeFilename', 'file_name' => $item]
                    ]], "message" => $descr]);
        }

        foreach ($to_st as $item) {
            foreach ($comments as $comment) {
                if (array_key_exists('media', $comment)) {
                    if(array_key_exists('document', $comment['media'])){
                        foreach ($comment['media']['document']['attributes'] as $att) {
                            if ($att['_'] == 'documentAttributeFilename') {
                                if ($att['file_name'] == $item) {
                                    $MTProto->MadelineProto->downloadToDir($comment['media'], $path . '/');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

        public function syncSubFolder($path)
        {
            $folders = scandir($path);
            $this->sync($path);
            foreach ($folders as $folder) {
                if (is_dir($path . '/' . $folder) && $folder != '- Theory' && !str_starts_with($folder, '@') && !str_starts_with($folder, '.')) {
                    $this->syncSubFolder($path . '/' . $folder);
                }
            }
        }
}
