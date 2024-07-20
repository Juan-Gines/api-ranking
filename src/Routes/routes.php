<?php

use Slim\Factory\AppFactory;
use App\Controllers\SongController;
use App\Services\SongService;


$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$songService = new SongService();
$songController = new SongController($songService);

$app->get('/ranking', [$songController, 'getRanking']);
$app->get('/ranking/{country}', [$songController, 'getRankingByCountry']);

$app->run();
