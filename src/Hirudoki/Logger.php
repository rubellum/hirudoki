<?php

namespace Hirudoki;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function write($message)
    {
        $log = new MonologLogger('default');

        $log->pushHandler(new StreamHandler(getenv('LOG_PATH'), MonologLogger::WARNING));

        $log->info($message);
    }
}