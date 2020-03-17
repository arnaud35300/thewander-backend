<?php

namespace App\Service;

use Snipe\BanBuilder\CensorWords;

class Censor
{
    public function check(string $content): bool
    {
        $censor = new CensorWords();

        $censor->addWhiteList([
            'password',
            'Password'
        ]);

        $censor->setDictionary([
            'en-us', 'en-uk', 'es', 'fr', 'nl', 'no', 'de', 'fi', 'it', 'jp'
        ]);

        $censoredContent = $censor->censorString($content);

        if (count($censoredContent['matched']) !== 0) 
            return false;
        
        return true;
    }
}