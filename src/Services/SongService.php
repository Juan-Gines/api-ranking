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

  public function getSongs($limit = 10, $country = null)
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
}
