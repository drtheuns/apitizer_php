<?php

use Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

if (file_exists(__DIR__.'/../.env')) {
    $dotenv = Dotenv::create(__DIR__.'/../');
    $dotenv->load();
}
