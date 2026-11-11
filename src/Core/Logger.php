<?php
namespace App\Core;

use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;

class Logger
{
    private static ?Monolog $log = null;

    public static function log(): Monolog
    {
        if (self::$log === null) {
            self::$log = new Monolog('app');
            self::$log->pushHandler(new StreamHandler(
                __DIR__ . '/../../storage/logs/app-' . date('Y-m-d') . '.log',
                Monolog::DEBUG
            ));
        }
        return self::$log;
    }
}