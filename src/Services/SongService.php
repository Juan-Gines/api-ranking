<?php

namespace App\Services;

use App\Exceptions\FileNotFoundException;
use App\Exceptions\InvalidCountryException;
use App\Exceptions\JsonDecodeException;
use App\Exceptions\SongNotFoundException;
use App\Models\Song;
use App\Utils\MessageLoader;

class SongService
{
  private $songs = [];
  private $lastId = 0;
  private $lastScore = 0;
  private $file_path = __DIR__ . '/../../data/songs.json';

  public function __construct()
  {
    if (!file_exists($this->file_path)) {
      throw new FileNotFoundException(MessageLoader::getMessage('error.file_not_found'));
    }
    $songData = json_decode(file_get_contents($this->file_path), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new JsonDecodeException(MessageLoader::getMessage('error.json_decode_error'));
    }

    if (count($songData) === 0) {
      throw new SongNotFoundException(MessageLoader::getMessage('error.song_list_empty'));
    }

    $this->songs = array_map(function ($song) {
      return Song::fromArray($song);
    }, $songData);

    $this->lastId = max(array_column($this->songs, 'id'));
    $this->lastScore = max(array_column($this->songs, 'score'));
  }

  private function saveSongs()
  {
    file_put_contents($this->file_path, json_encode($this->songs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new JsonDecodeException(MessageLoader::getMessage('error.json_encode_error'));
    }
  }

  public function getSongs($limit, $country = null)
  {
    $songs = $this->songs;
    if ($country) {
      $songs = array_filter($songs, function ($song) use ($country) {
        return $song->country === $country;
      });
    }

    if (count($songs) === 0) {
      throw new \InvalidArgumentException(MessageLoader::getMessage('error.incorrect_country'));
    } else {
      usort($songs, function ($a, $b) {
        return $a->score <=> $b->score;
      });
    }

    return array_slice($songs, 0, $limit);
  }

  public function addSong($title, $country)
  {
    $this->lastId++;
    $this->lastScore++;
    $song = new Song($this->lastId, $title, $this->lastScore, $country, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
    array_push($this->songs, $song);
    $this->saveSongs();
    return $song;
  }

  public function getSong($id)
  {
    $song = array_filter($this->songs, function ($song) use ($id) {
      return $song->id == $id;
    });
    if (count($song) === 0) {
      throw new SongNotFoundException(MessageLoader::getMessage('error.song_not_found'));
    }
    return array_shift($song);
  }

  public function updateSong($id, $title, $country)
  {
    $song = $this->getSong($id);
    $song->title = $title;
    $song->country = $country;
    $song->updateModificationDate();
    $this->saveSongs();
    return $song;
  }

  public function deleteSong($id)
  {
    $songToDelete = $this->getSong($id);
    $this->songs = array_filter($this->songs, function ($song) use ($id) {
      return $song->id != $id;
    });

    $this->songs = array_values($this->songs);

    $this->songs = array_map(function ($song) use ($songToDelete) {
      if ($song->score > $songToDelete->score) {
        $song->score--;
        $song->updateModificationDate();
      }
      return $song;
    }, $this->songs);

    $this->saveSongs();
    return $songToDelete;
  }

  public function touchSong($id)
  {
    $song = $this->getSong($id);
    if (!$song) {
      return null;
    }
    $score = $song->score;
    if ($score == 1) {
      return $song;
    }
    $songToSwap = $this->getSongByScore($score - 1);
    $song->score--;
    $song->updateModificationDate();
    if ($songToSwap) {
      $songToSwap->score++;
      $songToSwap->updateModificationDate();
    }
    $this->saveSongs();
    return $song;
  }

  private function getSongByScore($score)
  {
    $song = array_filter($this->songs, function ($song) use ($score) {
      return $song->score == $score;
    });
    return array_shift($song);
  }
}
