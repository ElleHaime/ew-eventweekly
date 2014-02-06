<?php
/**
 * Class Logger
 *
 * Wrapper for \Phalcon\Logger\Adapter\File
 *
 * Simple examples:
 * <code>
 * \Core\Logger::log('Debug text', \Phalcon\Logger::DEBUG);
 *
 * \Core\Logger::error('Oops! Some error!');
 *
 * \Core\Logger::log(array('one' => 1, 'two' => 2));
 *
 * \Core\Logger::error($object);
 * </code>
 *
 * Example with custom file
 * <code>
 * \Core\Logger::logFile('debugLog');
 * \Core\Logger::log('Error!', \Phalcon\Logger::DEBUG);
 * </code>
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core;

use Phalcon\Exception;
use \Phalcon\Logger\Adapter\File as FileAdapter;


class Logger {

    /**
     * @var FileAdapter
     */
    private static $Logger = null;

    /**
     * Path to folder with logs files
     *
     * @var null
     */
    private static $logFolderPath = null;

    /**
     * Log file
     *
     * @var string
     */
    private static $logFile = 'debug';

    private function __construct()
    {}

    private function __clone()
    {}

    /**
     * Constructor
     */
    private static function initialize($reset = false)
    {
        if (self::$logFolderPath === null) {
            self::$logFolderPath = ROOT_APP.'var/logs/';
        }

        if ($reset === true) {
            self::$Logger = null;
        }

        if (!self::instanceExist()) {
            self::$Logger = new FileAdapter(self::$logFolderPath.self::$logFile.'.log');
        }
    }

    private static function instanceExist()
    {
        return (self::$Logger instanceof FileAdapter);
    }

    /**
     * Reset logger instance
     *
     * @return FileAdapter
     */
    public static function resetInstance()
    {
        self::initialize(true);
        return self::$Logger;
    }

    /**
     * Set log file name
     *
     * @param $fileName
     * @return FileAdapter
     */
    public static function logFile($fileName)
    {
        self::$logFile = $fileName;
        self::resetInstance();
        return self::$Logger;
    }

    /**
     * Call methods of \Phalcon\Logger\Adapter\File class
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!self::instanceExist()) {
            self::initialize();
        }

        if (is_array($arguments[0]) || is_object($arguments[0])) {

            if (is_object($arguments[0]) && method_exists($arguments, '__toString')) {
                $stringObj = $arguments[0]->__toString();
            }else {
                ob_start();
                print_r($arguments[0]);
            }

            if (isset($stringObj) && !empty($stringObj)) {
                $result = $stringObj;
            }else {
                $result = ob_get_flush();
                ob_clean();
            }
            $arguments[0] = $result;
        }

        return call_user_func_array(array(self::$Logger, $name), $arguments);
    }

} 