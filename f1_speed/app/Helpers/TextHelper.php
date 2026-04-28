<?php
namespace App\Helpers;

class TextHelper{
    public static function parseHashtags(string $text): string
    {
        return preg_replace(
            '/#(\w+)/u',
            '<a href="/search?q=$1" class="text-[#3FA9F5] hover:underline font-semibold">#$1</a>',
            e($text)
        );
    }
}