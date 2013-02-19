<?php

/**
 * Simple Logger class
 *
 * @author staabm
 */
abstract class rex_logger extends rex_factory_base
{
    private static $handle;

    /**
     * Logs the given Exception
     *
     * @param Exception $exception The Exception to log
     */
    public static function logException(Exception $exception)
    {
        if ($exception instanceof ErrorException) {
            self::logError($exception->getSeverity(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
        } else {
            self::log('<div><b>' . get_class($exception) . '</b>: ' . $exception->getMessage() . ' in <b>' . $exception->getFile() . '</b> on line <b>' . $exception->getLine() . '</b></div>', E_USER_ERROR);
        }
    }

    /**
     * Logs a error message
     *
     * @param integer $errno   The error code to log
     * @param string  $errstr  The error message
     * @param string  $errfile The file in which the error occured
     * @param integer $errline The line of the file in which the error occured
     */
    public static function logError($errno, $errstr, $errfile, $errline)
    {
        if (!is_int($errno)) {
            throw new rex_exception('Expecting $errno to be integer, but ' . gettype($errno) . ' given!');
        }
        if (!is_string($errstr)) {
            throw new rex_exception('Expecting $errstr to be string, but ' . gettype($errstr) . ' given!');
        }
        if (!is_string($errfile)) {
            throw new rex_exception('Expecting $errfile to be string, but ' . gettype($errfile) . ' given!');
        }
        if (!is_int($errline)) {
            throw new rex_exception('Expecting $errline to be integer, but ' . gettype($errline) . ' given!');
        }

        self::log('<div><b>' . rex_error_handler::getErrorType($errno) . "</b>[$errno]: $errstr in <b>$errfile</b> on line <b>$errline</b></div>", $errno);
    }

    /**
     * Logs the given message
     *
     * @param string $message the message to log
     * @param int    $errno
     * @throws rex_exception
     */
    public static function log($message, $errno = E_USER_ERROR)
    {
        if (static::hasFactoryClass()) {
            static::callFactoryClass(__FUNCTION__, func_get_args());
            return;
        }

        if (!is_string($message)) {
            throw new rex_exception('Expecting $message to be string, but ' . gettype($message) . ' given!');
        }

        self::open();
        if (is_resource(self::$handle)) {
            fwrite(self::$handle, '<div>' . date('r') . '</div>' . $message . "\n");

            // forward the error into phps' error log
            error_log($message, 0);
        }
    }

    /**
     * Prepares the logifle for later use
     */
    public static function open()
    {
        // check if already opened
        if (!self::$handle) {
            $file = rex_path::cache('system.log');
            self::$handle = fopen($file, 'ab');

            if (!self::$handle) {
                echo 'Error while creating logfile ' . $file;
                exit();
            }
        }
    }

    /**
     * Closes the logfile. The logfile is not be able to log further message after beeing closed.
     *
     * You dont need to close the logfile manually when it was registered during the request.
     */
    public static function close()
    {
        if (is_resource(self::$handle)) {
            fclose(self::$handle);
        }
    }
}
