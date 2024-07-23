<?php

namespace App\Exceptions;

class JsonDecodeException extends \Exception
{
  protected $message;

  public function __construct($message)
  {
    $this->message = $message;
  }
}
