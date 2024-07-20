<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';


$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response, $args) {
  $response->getBody()->write("Hola, ranking!");
  return $response;
});

$app->run();
