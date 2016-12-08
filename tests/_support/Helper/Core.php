<?php

namespace Helper;


/**
 * Class Core. Some helpers for acceptance tests
 * @package Helper
 */
class Core
{
    /**
     * Get valid captcha value for register/login/etc forms
     * @return bool|string
     */
    public static function getCaptcha()
    {
        $root = realpath(__DIR__ . '/../../../');
        if (!file_exists($root . '/Private/Config/Default.php')) {
            return false;
        }

        return md5_file($root . '/Private/Config/Default.php');
    }
}