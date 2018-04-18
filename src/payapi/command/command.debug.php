<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->debug(string)
*
* @TYPE     public
*
* @RETURNS
*           debugged string || error
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["error"]=>
*           string(15) "debugged string"
*
* @NOTE    deprecated, command is exec from engine
*/

final class commandDebug extends controller
{
    public function run()
    {
        if ($this->arguments(0) !== false && is_string($this->arguments(0)) === true) {
            $this->debug($this->arguments(0), 'debug');
            return $this->render($this->arguments(0));
        }
        return $this->returnResponse($this->error->badRequest());
    }

}
