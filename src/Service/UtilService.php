<?php

namespace App\Service;

class UtilService
{
    public function getUniqid(string $str = ''): string
    {
        return $str . str_replace('.', '', uniqid('', true));
    }

    public function strEndsWith(string $haystack, string $needle): bool
    {
        $tab = explode($needle, $haystack);

        if ('' === end($tab)) {
            return true;
        }

        return false;
    }

    /**
     * strContains('abc', 'a')
     */
    public function strContains(string $haystack, string $needle): bool
    {
        return false === strpos($haystack, $needle) ? false : true;
    }
}