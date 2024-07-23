<?php

namespace App\Utils;

class MessageLoader
{
  private static $messages = null;

  public static function getMessages()
  {
    if (self::$messages === null) {
      self::$messages = require __DIR__ . '/../../config/messages.php';
    }
    return self::$messages;
  }

  public static function getMessage($key)
  {
    $keys = explode('.', $key);
    $messages = self::getMessages();

    foreach ($keys as $k) {
      if (!isset($messages[$k])) {
        return $key;
      }
      $messages = $messages[$k];
    }

    return $messages;
  }
}
