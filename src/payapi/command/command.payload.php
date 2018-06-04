<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->payload($data)
*
* @TYPE     private
*
* @RETURNS
*           encoded payload
*
*/

final class commandPayload extends controller
{

    public function run()
    {
        if ($this->arguments(0) ==! $this->serialize->undefined()) {
            return $this->render($this->payload($this->arguments(0)));
        }
        return $this->returnResponse($this->error->badRequest());
    }

    private function payload($data)
    {
        return $this->encode($data, $this->apiKey());
    }
    //-> @NOTE not used by now
    private function merchantPayload()
    {
        $partial = $this->partialPayments();
        if ($this->publicId() !== false && is_array($partial) === true) {
            $payload = array(
                "storeDomain" => getenv('SERVER_NAME')
            );
            return $this->payload($payload);
        }
        return false;
    }
}
