<?php

namespace App\Exceptions;

class FileNotFoundException extends \Exception
{
  protected $message;

  public function __construct($message)
  {
    $this->message = $message;
  }
}
