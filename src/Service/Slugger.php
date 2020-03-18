<?php

namespace App\Service;

class Slugger
{
    /**
     * Slugifies a string to make it fit into the client's URL.
     * 
     * @param string $string The string to slugify.
     * 
     * @return string
     */
    public function slugify(string $string): string
    {
        return preg_replace(
            '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', strtolower(trim(strip_tags($string))) 
        );
    }
}