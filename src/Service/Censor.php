<?php

namespace App\Service;

use Snipe\BanBuilder\CensorWords;

class Censor
{
    /**
     *? Verifies and strikes or validates the request's content.
     * 
     * @param string $content The client's content.
     * 
     * @return bool
     */
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