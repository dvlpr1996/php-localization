<?php

/**
 * @Package: Php Localization Package
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

/**
 * Tells Whether A File Exists And Is A Regular File
 * @param string $fileDir
 * @return bool
 */
if (!function_exists('checkFile')) {
    function checkFile(string $fileDir): bool
    {
        return (!is_readable($fileDir) && !is_file($fileDir)) ? false : true;
    }
}

/**
 * Sanitize Values In Language Files
 * @param string string
 * @return string
 */
if (!function_exists('safeText')) {
    function safeText(string $string): string
    {
        $string = stripslashes(trim($string));
        $string = strip_tags($string);
        $string = preg_replace('/\s+/im', ' ', trim($string));
        $string = html_entity_decode(htmlentities($string));
        $string = htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
        return trim($string);
    }
}
