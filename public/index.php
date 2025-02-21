<?php

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Incluir el router.php para manejar las rutas
require_once __DIR__ . '/../src/routing/router.php';
