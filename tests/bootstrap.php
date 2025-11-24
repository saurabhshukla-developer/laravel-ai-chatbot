<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// adjust path to where your package .env lives; e.g. package root
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
