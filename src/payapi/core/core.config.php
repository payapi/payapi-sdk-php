<?php

namespace payapi;

//-> SDK config
final class config
{

  public static
    $single                     =      false;

  private
    $settings                   =      false,
    $instance                   =      false,
    $schema                     =      array(
      "debug"                   =>     false,
      "staging"                 =>     false,
      "demo"                    =>     false
    )                                       ;

  private function __construct($config)
  {
    $this->instance = instance::this();
    foreach($this->schema as $key => $value) {
      if (isset($config[$key]) === true) {
        if ($config[$key] === true) {
          $this->settings[$key] = true;
        } else {
          $this->settings[$key] = $value;
        }
      }
    }
  }

  public function __call($key, $arguments = array())
  {
    //var_dump($key, $arguments);
    if($key === 'mode') {
      return $this->mode($arguments);
    }
    if ($arguments === array() && is_string($key) === true && isset($this->settings[$key]) === true) {
      return $this->settings[$key];
    }
    //->  @NOTE
    return null;
  }

  public function mode($data = false)
  {
    if(isset($data[0]) === true && $data[0] === true) {
      $this->settings['staging'] = true;
    } else {
      $this->settings['staging'] = false;
    }
    return $this->settings['staging'];
  }

  public static function single($config = array())
  {
    if (self::$single === false) {
      self::$single = new self($config);
    }
    return self::$single;
  }

  public function __toString()
  {
    return $this->data;
  }


}
