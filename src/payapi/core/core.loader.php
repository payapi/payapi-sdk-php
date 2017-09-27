<?php

namespace payapi;

final class loader extends helper
{

  private
    $schema                    = array(),
    $loaded                    = array();

  public function checkCommand($command)
  {
    //->
    if (is_string($command) === true && $this->checkCommandFile($command) === true) {
      return true;
    }
    return false;
  }

  private function checkCommandFile($key)
  {
    $commandFile = $this->route->command($key);
    if (isset($commadFile) === true) {
      return true;
    }
    $this->warning('not available', 'command');
    return false;
  }

  public function command($key)
  {
    if (is_string($route = $this->route->command($key)) === true) {
      if (isset($this->loaded[$key]) !== true) {
        require_once($route);
        $this->loaded[$key] = true;
      }
      return true;
    }
    return false;
  }

  public function schema($key)
  {
    if (isset($this->schema[$key]) === true) {
      return $this->schema[$key];
    } else
    if (is_string($route = $this->route->schema($key)) === true) {
      $schema = json_decode(file_get_contents($route), true);
      $this->schema[$key] = $schema;
      return $schema;
    }
    $this->warning('[' . $key . '] not available', 'schema');
    return false;
  }


}
