<?php
/**
 *
 * Created by Dream.
 * User: Boxjan
 * Datetime: 2019-01-13 17:49
 */

namespace App;



class Log
{
    /**
     * @param $message
     * Log::info("user: {}  ip: {}  balabala", $user, $ip)
     */
    public static function info($message) {
        @self::saveLog($message, __FUNCTION__, func_get_args());
    }

    /**
     * @param $message
     * Log::warn("user: {}  ip: {}  balabala", $user, $ip)
     */
    public static function warning($message) {
        @self::saveLog($message, __FUNCTION__, func_get_args());
    }

    /**
     * @param $message
     * Log::error("user: {}  ip: {}  balabala", $user, $ip)
     */
    public static function error($message) {
        @self::saveLog($message, __FUNCTION__, func_get_args());
    }

    /**
     * @param $message
     * Log::debug("user: {}  ip: {}  balabala", $user, $ip)
     * before record message, it will check APP_DEBUG status
     */
    public static function debug($message) {
        if (env('APP_DEBUG', false) === true)
            @self::saveLog($message, __FUNCTION__, func_get_args());
    }

    private static function dealBacktrace() {
        if (version_compare(PHP_VERSION,'5.3.6','<')) {
            $backtrace = debug_backtrace();
        } else {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        }

        $upstream = array();
        foreach ($backtrace as $stackInfo) {
            if ($stackInfo['class'] != __CLASS__) {
                $upstream['function'] = $stackInfo['function'];
                $upstream['class'] = $stackInfo['class'];
                break;
            } else {
                $upstream['line'] = $stackInfo['line'];
                $upstream['file'] = $stackInfo['file'];
            }
        }
        return $upstream;
    }

    private static function parseMessage($message, $args) {
        $arguments = array();
        array_shift($args);
        foreach ($args as $arg) {
            if (is_array($arg) || is_object($arg)) {
                array_push($arguments, json_encode($arg));
            } else {
                array_push($arguments, $arg);
            }
        }

        $sizeOfArgs = sizeof($arguments);
        $sizeOfPlace = substr_count($message,'{}');

        if($sizeOfArgs > $sizeOfPlace) {
            for($i = $sizeOfArgs - $sizeOfPlace; $i > 0; $i--) {
                $message .=" {}";
            }
        } else if ($sizeOfArgs < $sizeOfPlace) {
            for($i = $sizeOfPlace - $sizeOfArgs; $i > 0; $i--) {
                array_push($arguments, "[PlaceHolder]");
            }
        }

        $message = str_replace('{}', '%s', $message);
        return vsprintf($message, $arguments);
    }

    private static function saveLog($message, $level, $arguments) {
        $trace = self::dealBacktrace();
        $message = self::parseMessage($message, $arguments);

        $logMsg = sprintf("[%s] [%s:%s] - %s",
            $trace['class'], $trace['function'], $trace['line'], $message);

        \Illuminate\Support\Facades\Log::channel('app')->$level($logMsg);
    }
}