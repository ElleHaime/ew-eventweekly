<?php
/**
 * Class CoreTag
 *
 * @author   Slava Basko <basko.slava@gmail.com>
 */

namespace Core;

class CoreTag extends \Phalcon\Tag {

    static $config = null;

    public function __construct($config = null)
    {
        self::$config = $config;
    }

    public static function stylesheetLink($parameters = null, $local = null)
    {
        if (is_string($parameters) && self::$config->application->debug) {
            $parameters = $parameters.'?'.rand(0, 9999999);
        }
        return parent::stylesheetLink($parameters, $local);
    }

    public static function javascriptInclude($parameters = null, $local = null)
    {
        if (is_string($parameters) && self::$config->application->debug) {
            $parameters = $parameters.'?'.rand(0, 9999999);
        }
        return parent::javascriptInclude($parameters, $local);
    }

} 