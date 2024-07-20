<?php

namespace App\Services;

use App\Models\Song;

class SongService
{
  private $songs = [];
  private $lastId = 0;
  private $lastScore = 0;
  private $file_path = __DIR__ . '/../../data/songs.json';

  public function __construct()
  {
    if (file_exists($this->file_path)) {
      $songData = json_decode(file_get_contents($this->file_path), true);
      $this->songs = array_map(function ($song) {
        return new Song(
          $song['id'],
          $song['title'],
          $song['score'],
          $song['country'],
          $song['date_added'],
          $song['date_modified']
        );
      }, $songData);
      $this->lastId = max(array_column($this->songs, 'id'));
      $this->lastScore = max(array_column($this->songs, 'score'));
    } else {
      $this->songs = [
        new Song(1, 'Bohemian Rhapsody', 1, 'Inglaterra', '2024-01-01 10:00:00', '2024-01-01 10:00:00'),
        new Song(2, 'Hotel California', 2, 'Estados Unidos', '2024-01-01 10:00:00', '2024-01-01 10:00:00'),
      ];
      $this->lastId = 2;
      $this->lastScore = 2;
    }
  }

  private function saveSongs()
  {
    file_put_contents($this->file_path, json_encode($this->songs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  }

  public function getSongs($limit, $country = null)
  {
    $songs = $this->songs;
    if ($country) {
      $songs = array_filter($songs, function ($song) use ($country) {
        return $song->country === $country;
      });
    }
    usort($songs, function ($a, $b) {
      return $b->score <= $a->score;
    });
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
    return array_shift($song);
  }

  public function updateSong($id, $title, $country)
  {
    $song = $this->getSong($id);
    if (!$song) {
      return null;
    }
    $song->title = $title;
    $song->country = $country;
    $song->updateModificationDate();
    $this->saveSongs();
    return $song;
  }

  public function deleteSong($id)
  {
    $songToDelete = $this->getSong($id);
    if (!$songToDelete) {
      return false;
    }
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
