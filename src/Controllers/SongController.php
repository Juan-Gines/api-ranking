<?php

namespace App\Controllers;

use App\Services\SongService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SongController
{
  private $songService;

  public function __construct(SongService $songService)
  {
    $this->songService = $songService;
  }

  public function getRanking(Request $request, Response $response, $args)
  {
    $limit = $request->getQueryParams()['limit'] ?? 10;
    $songs = $this->songService->getSongs($limit);
    $response->getBody()->write(json_encode($songs));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getRankingByCountry(Request $request, Response $response, $args)
  {
    $country = ucwords($args['country']);
    $limit = $request->getQueryParams()['limit'] ?? 10;
    $songs = $this->songService->getSongs($limit, $country);
    $response->getBody()->write(json_encode($songs));
    return $response->withHeader('Content-Type', 'application/json');
  }
}
