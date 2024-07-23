<?php

use Slim\Factory\AppFactory;
use App\Controllers\SongController;
use App\Handlers\ExceptionHandler;
use App\Services\SongService;
use Selective\BasePath\BasePathMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(false, true, true);

try {

  $songService = new SongService();
  $songController = new SongController($songService);

  $app->get('/ranking', [$songController, 'getRanking']);
  $app->get('/ranking/{country}', [$songController, 'getRanking']);
  $app->post('/song', [$songController, 'addSong']);
  $app->get('/song/{id}', [$songController, 'getSong']);
  $app->put('/song/{id}', [$songController, 'updateSong']);
  $app->patch('/song/touch/{id}', [$songController, 'touchSong']);
  $app->delete('/song/{id}', [$songController, 'deleteSong']);
} catch (\Exception $e) {

  $app->get('/{routes:.+}', function (Request $request, Response $response) use ($e) {
    return ExceptionHandler::handle($response, $e);
  });
}

$app->run();
