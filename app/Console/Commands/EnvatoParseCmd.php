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

            $pattern = '#\<li class="[a-zA-Z-_/\.0-9\?%&;=\<\>\|"'. "'" .' :!\(\),]+?Download\<\/span\>\<\/div\>\<\/div\>\<\/div\>\<\/li\>#';
            $this->parse($url, $pattern, true);
        }
    }

    public function parse($url, $pattern, $mAll = false) {
        $html = $this->getHtml($url);
        if ($mAll) {
            preg_match_all($pattern,
            preg_replace('#amp;#', '', $html),
            $matches,PREG_PATTERN_ORDER);
            return $matches[0];
        } else {
            preg_match(
                $pattern,
                preg_replace('#amp;#', '', $html), $matches);
            return $matches;
        }
    }
}
