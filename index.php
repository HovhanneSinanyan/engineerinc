<?php
use Kernel\Router;
use Kernel\App;

require __DIR__.'/vendor/autoload.php';

$router = Router::getInstance();

require __DIR__.'/routes/router.php';






$app = new App($router);
session_start();
$app->captureRequest();
