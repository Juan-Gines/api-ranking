<?php

namespace App\Handlers;

use App\Exceptions\SongNotFoundException;
use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;

class ExceptionHandler
{
  public static function handle(Response $response, \Exception $e)
  {
    $status = 500;
    $error = $e->getMessage();

    if ($e instanceof SongNotFoundException) {
      $status = 404;
    } else if ($e instanceof ValidationException) {
      $status = 422;
    }
    $response->getBody()->write(json_encode([
      'status' => 'error',
      'error' => $error
    ]));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
  }
}
