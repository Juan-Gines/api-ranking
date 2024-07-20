<?php

use Slim\Factory\AppFactory;
use App\Controllers\SongController;
use App\Services\SongService;


$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$songService = new SongService();
$songController = new SongController($songService);

$app->get('/ranking', [$songController, 'getRanking']);
$app->get('/ranking/{country}', [$songController, 'getRankingByCountry']);
$app->post('/song', [$songController, 'addSong']);
$app->get('/song/{id}', [$songController, 'getSong']);
$app->put('/song/{id}', [$songController, 'updateSong']);
$app->patch('/song/touch/{id}', [$songController, 'touchSong']);
$app->delete('/song/{id}', [$songController, 'deleteSong']);

$app->run();
