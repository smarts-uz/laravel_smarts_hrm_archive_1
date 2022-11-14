<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnvatoParseCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envato:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->envatoParse('https://elements.envato.com','wordpress');
    }

    public function envatoParse ($link, $category = NULL, $filter = NULL) {
        for ($i = 1; $i < 50; $i++) {
            $url = $link;
            if ($category) {$url .= '/' . $category;}
            if ($filter) {$url.= '/' . $filter;}
            if ($i > 1) {$url .= '/pg-' . $i;}

            $pattern = '#(?:<li class="[- a-zA-Z0-9]+"><div class="[a-zA-Z]+" data-test-selector="[- a-zA-Z]+"><a title="[- a-zA-Z0-9&]+" class="[a-zA-Z_0-9]+" href=")([\/a-zA-Z0-9-]+)"><\/a>#';
            $parsed =  $this->parse($url, $pattern, true);
            $links = array();
            $itemPattern = '#(https://video-previews[a-z-_/\.0-9]+)((?<=src=")https://elements-cover-images[a-zA-Z-_/\.0-9\?%&;=]+)#';
            foreach ($parsed[1] as $item) {
                $links[] = $this->parse($link . $item, $itemPattern, true);
            }
        }
        return $i;
    }

    public function parse($url, $pattern, $mAll = false) {
        $html = $this->getHtml($url);
        if ($mAll) {
            preg_match_all($pattern,
            preg_replace('#amp;#', '', $html),
            $matches,PREG_PATTERN_ORDER);
            return $matches;
        } else {
            preg_match(
                $pattern,
                preg_replace('#amp;#', '', $html), $matches);
            return $matches;
        }
    }

    public function getHtml($url) {
        // 1. инициализация
        $ch = curl_init();
        // 2. указываем параметры, включая url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT,
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0');
        // 3. получаем HTML в качестве результата
        $subject = curl_exec($ch);
        // 4. закрываем соединение
        curl_close($ch);
        return $subject;
    }
}
