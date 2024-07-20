<?php

namespace App\Models;

class Song
{
  public $id;
  public $title;
  public $score;
  public $country;
  public $date_added;
  public $date_modified;

  public function __construct($id, $title, $score, $country, $date_added, $date_modified)
  {
    $this->id = $id;
    $this->title = $title;
    $this->score = $score;
    $this->country = $country;
    $this->date_added = $date_added;
    $this->date_modified = $date_modified;
  }

  public function updateModificationDate()
  {
    $this->date_modified = date('Y-m-d H:i:s');
  }
}
