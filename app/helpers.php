<?php


if (!function_exists('sanitize_string')) {
    function sanitize_string(string $str, bool $defaultNull = true)
    {
        $str = strip_tags($str);
        $str = trim($str);
        if (empty($str) && $defaultNull) return null;
        return $str;
    }
}
