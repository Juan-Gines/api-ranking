<?php

namespace App\Exceptions;

class ValidationException extends \Exception
{
  protected $message;

  public function __construct($message)
  {
    $this->message = $message;
  }
}
