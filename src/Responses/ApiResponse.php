<?php

namespace App\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class ApiResponse
{
  public static function respondWithJson(Response $response, $data, $status = 200)
  {
    $response->getBody()->write(json_encode([
      'status' => 'success',
      'data' => $data,
    ]));
    return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
  }
}
