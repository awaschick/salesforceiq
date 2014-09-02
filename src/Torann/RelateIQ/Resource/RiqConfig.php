<?php namespace Torann\RelateIQ\Resource;

class RiqConfig {

    /**
     * API authentication key.
     *
     * @param string
     */
    public static $key = "";

    /**
     * API authentication secret.
     *
     * @param string
     */
    public static $secret = "";

    /**
     * Set authentication values.
     *
     * @param string $key
     * @param string $secret
     */
    public static function setKey($key, $secret)
    {
        self::$key    = $key;
        self::$secret = $secret;
    }
}
