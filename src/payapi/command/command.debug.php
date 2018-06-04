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
        if ($this->arguments(0) !== $this->serialize->undefined() && is_string($this->arguments(0)) === true) {
        	$debug = $this->sanitize->string($this->arguments(0));
            $this->debug($debug, 'debug');
            return $this->render($debug);
        }
        return $this->returnResponse($this->error->badRequest());
    }

}
