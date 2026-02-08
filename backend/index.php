<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Bootstrap\Application;
use App\Database\DataBaseModule;
use App\Exceptions\ExceptionHandler;

set_exception_handler(function ($e) {
    $handler = new ExceptionHandler();
    $handler->handle($e);
});

set_error_handler(/**
 * @throws ErrorException
 */ function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    DataBaseModule::runMigrations();
} catch (Exception $e) {
    error_log('Migration error: ' . $e->getMessage());
}

$app = new Application();
$app->run();
