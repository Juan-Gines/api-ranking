<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Handlers\ExceptionHandler;
use App\Responses\ApiResponse;
use App\Services\SongService;
use App\Utils\MessageLoader;
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
    try {
      $country = isset($args['country']) ? ucwords($args['country']) : null;
      $limit = $request->getQueryParams()['limit'] ?? 500;
      if (!is_numeric($limit)) {
        throw new ValidationException(MessageLoader::getMessage('validation.invalid_limit'));
      }
      $songs = $this->songService->getSongs($limit, $country);
      return ApiResponse::respondWithJson($response, $songs);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  public function addSong(Request $request, Response $response, $args)
  {
    try {
      $body = $request->getParsedBody();

      if (!$this->validateSong($body)) {
        throw new ValidationException(MessageLoader::getMessage('validation.title_country_required'));
      }

      $title = $body['title'];
      $country = ucwords($body['country']);
      $song = $this->songService->addSong($title, $country);
      return ApiResponse::respondWithJson($response, $song, 201);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  public function getSong(Request $request, Response $response, $args)
  {
    try {
      $id = $args['id'];
      if (!is_numeric($id)) {
        throw new ValidationException(MessageLoader::getMessage('validation.invalid_id'));
      }
      $song = $this->songService->getSong($id);

      return ApiResponse::respondWithJson($response, $song);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  public function updateSong(Request $request, Response $response, $args)
  {
    try {
      $id = $args['id'];
      if (!is_numeric($id)) {
        throw new ValidationException(MessageLoader::getMessage('validation.invalid_id'));
      }
      $body = $request->getParsedBody();

      if (!$this->validateSong($body)) {
        throw new ValidationException(MessageLoader::getMessage('validation.title_country_required'));
      }

      $title = $body['title'];
      $country = ucwords($body['country']);
      $song = $this->songService->updateSong($id, $title, $country);

      return ApiResponse::respondWithJson($response, $song);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  public function deleteSong(Request $request, Response $response, $args)
  {
    try {
      $id = $args['id'];
      if (!is_numeric($id)) {
        throw new ValidationException(MessageLoader::getMessage('validation.invalid_id'));
      }
      $song = $this->songService->deleteSong($id);

      return ApiResponse::respondWithJson($response, $song);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  public function touchSong(Request $request, Response $response, $args)
  {
    try {
      $id = $args['id'];
      if (!is_numeric($id)) {
        throw new ValidationException(MessageLoader::getMessage('validation.invalid_id'));
      }
      $song = $this->songService->touchSong($id);

      return ApiResponse::respondWithJson($response, $song);
    } catch (\Exception $e) {
      return ExceptionHandler::handle($response, $e);
    }
  }

  private function validateSong($song)
  {
    return (isset($song['title']) || isset($song['country'])) && (is_string($song['title']) && is_string($song['country']));
  }
}
