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
    $limit = $request->getQueryParams()['limit'] ?? 500;
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

  public function addSong(Request $request, Response $response, $args)
  {
    $body = $request->getParsedBody();

    if (!$this->validateSong($body)) {
      $response->getBody()->write(json_encode(['error' => 'Título y país son requeridos']));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $title = $body['title'];
    $country = ucwords($body['country']);
    $song = $this->songService->addSong($title, $country);
    $response->getBody()->write(json_encode($song));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function getSong(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    $song = $this->songService->getSong($id);

    if (!$song) {
      $response->getBody()->write(json_encode(['error' => 'Canción no encontrada']));
      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($song));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function updateSong(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    $body = $request->getParsedBody();

    if (!$this->validateSong($body)) {
      $response->getBody()->write(json_encode(['error' => 'Título y país son requeridos']));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $title = $body['title'];
    $country = ucwords($body['country']);
    $song = $this->songService->updateSong($id, $title, $country);

    if (!$song) {
      $response->getBody()->write(json_encode(['error' => 'Canción no encontrada']));
      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($song));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function deleteSong(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    $song = $this->songService->deleteSong($id);

    if (!$song) {
      $response->getBody()->write(json_encode(['error' => 'Canción no encontrada']));
      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($song));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function touchSong(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    $song = $this->songService->touchSong($id);

    if (!$song) {
      $response->getBody()->write(json_encode(['error' => 'Canción no encontrada']));
      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($song));
    return $response->withHeader('Content-Type', 'application/json');
  }

  private function validateSong($song)
  {
    if (!isset($song['title']) || !isset($song['country'])) {
      return false;
    }
    return true;
  }
}
