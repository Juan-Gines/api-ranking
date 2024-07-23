<?php

namespace App\Exceptions;

class SongNotFoundException extends \Exception
{
  protected $message;

  public function __construct($message)
  {
    $this->message = $message;
  }
}
