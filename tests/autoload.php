<?php

use Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

if (file_exists(__DIR__.'/../.env')) {
    $dotenv = Dotenv::create(__DIR__.'/../');
    $dotenv->load();
} else {
    echo "You need to set up a .env file in order to begin testing\n";
    echo "Refer to the SETUP.md for more information\n";

    exit(1);
}
