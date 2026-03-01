<?php
/**
 * EventCore — Constantes Globales
 */
define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', '/drop/tecnoloweb2');
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error.log');

// Zona horaria
date_default_timezone_set('America/La_Paz');
