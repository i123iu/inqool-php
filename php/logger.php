<?php

class Logger
{
    function log_error(string $error): void
    {
        if (!isset($GLOBALS['error']))
            $GLOBALS['error'] = '';
        $GLOBALS['error'] = $error . "\n" . $GLOBALS['error'];
    }

    function get_all_errors(): string
    {
        return $GLOBALS['error'] ?? '';
    }
}