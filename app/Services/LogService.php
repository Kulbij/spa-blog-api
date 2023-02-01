<?php

namespace App\Services;

use Log;

class LogService
{
    /**
     * @param $message
     * @param $e
     * @param $code
     * @param array $params
     */
    public static function exception($message, $e, $code, $params = []): void
    {
        $message .= ' ' . get_class($e) . ' ' . $e->getMessage() . "\nin " . $e->getFile() . ':' . $e->getLine();
        $message .= "\nStack trace:\n" . $e->getTraceAsString();
        $params['error_symbol_code'] = $code;

        Log::error($message, array_filter($params));

        return;
    }

    public static function error($message, $e = null, $code = '', $params = [])
    {
        if (is_object($e)) {
            $message .= ' ' . get_class($e) . ' ' . $e->getMessage() . "\nin " .
                $e->getFile() . ':' . $e->getLine() . "\nStack trace:\n" . $e->getTraceAsString();
        } elseif (is_string($e) && !empty($e)) {
            $message .= ' ' . $e;
        }

        $params['error_symbol_code'] = $code;

        Log::error($message, array_filter($params));

        return;
    }

    public static function warning($message, $e = null, $code = '', $params = [])
    {
        if (is_object($e)) {
            $message .= ' ' . get_class($e) . ' ' . $e->getMessage() .
                "\nin " . $e->getFile() . ':' . $e->getLine() . "\nStack trace:\n" . $e->getTraceAsString();
        } elseif (is_string($e) && !empty($e)) {
            $message .= ' ' . $e;
        }

        $params['error_symbol_code'] = $code;

        Log::warning($message, array_filter($params));

        return;
    }

    public static function info($message, $e = null, $code = '', $params = [])
    {
        if (is_string($e) && !empty($e)) {
            $message .= ' ' . $e;
        }

        $params['error_symbol_code'] = $code;

        Log::info($message, array_filter($params));

        return;
    }

    public static function debug($message, $e = null, $code = '', $params = [])
    {
        if (env('APP_ENV') == 'production') {
            return;
        }

        if (is_string($e) && !empty($e)) {
            $message .= ' ' . $e;
        }

        $params['error_symbol_code'] = $code;

        Log::debug($message, array_filter($params));

        return;
    }

    public static function notice($message, $e = null, $code = '', $params = [])
    {
        if (is_string($e) && !empty($e)) {
            $message .= ' ' . $e;
        }

        $params['error_symbol_code'] = $code;

        Log::notice($message, array_filter($params));

        return;
    }
}
