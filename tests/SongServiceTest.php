<?php

use PHPUnit\Framework\TestCase;
use App\Services\SongService;
use App\Exceptions\SongNotFoundException;
use App\Utils\MessageLoader;

class SongServiceTest extends TestCase
{
  private $songService;

  protected function setUp(): void
  {
    $this->songService = new SongService(__DIR__ . '/../data/testsongs.json');
  }

  public function testGetSongsReturnsCorrectNumberOfSongs()
  {
    $songs = $this->songService->getSongs(5);
    $this->assertCount(5, $songs);
  }

  public function testGetSongsReturnsSongsWithCorrectCountry()
  {
    $songs = $this->songService->getSongs(5, 'Estados Unidos');
    foreach ($songs as $song) {
      $this->assertEquals('Estados Unidos', $song->country);
    }
  }

  public function testGetSongsThrowsExceptionWhenIncorrectCountryIsProvided()
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage(MessageLoader::getMessage('error.incorrect_country'));
    $this->songService->getSongs(5, 'País Incorrecto');
  }

  public function testAddSongIncreasesSongCount()
  {
    $initialCount = count($this->songService->getSongs(40));
    $this->songService->addSong('Nueva Canción', 'Estados Unidos');
    $newCount = count($this->songService->getSongs(40));
    $this->assertEquals($initialCount + 1, $newCount);
  }

  public function testGetSongReturnsCorrectSong()
  {
    $song = $this->songService->getSong(3);
    $this->assertEquals(3, $song->id);
  }

  public function testGetSongThrowsExceptionWhenSongNotFound()
  {
    $this->expectException(SongNotFoundException::class);
    $this->expectExceptionMessage(MessageLoader::getMessage('error.song_not_found'));
    $this->songService->getSong(100);
  }

  public function testUpdateSongUpdatesSongTitleAndCountry()
  {
    $song = $this->songService->getSong(5);
    $updatedSong = $this->songService->updateSong(5, 'Canción actualizada', 'Inglaterra');
    $this->assertEquals('Canción actualizada', $updatedSong->title);
    $this->assertEquals('Inglaterra', $updatedSong->country);
  }

  public function testTouchSongDecreasesScoreOfSong()
  {
    $song = $this->songService->getSong(21);
    $initialScore = $song->score;
    $touchedSong = $this->songService->touchSong(21);
    $this->assertEquals($initialScore - 1, $touchedSong->score);
  }

  public function testDeleteSongRemovesSongFromList()
  {
    $initialCount = count($this->songService->getSongs(30));
    $deletedSong = $this->songService->deleteSong(21);
    $newCount = count($this->songService->getSongs(30));
    $this->assertEquals($initialCount - 1, $newCount);
    $this->assertEquals(21, $deletedSong->id);
  }
}
