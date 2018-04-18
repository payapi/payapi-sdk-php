<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->ssl()      Validate server ssl certificate
*                             returns cache if available and not expired
*
*           $sdk->ssl(true)   forces validation if cached
*
* @TYPE     public
*
* @RETURNS
*           success/error ssl validation response
*
* @SAMPLE
*          ["code"]=>
*           int(505)
*          ["error"]=>
*           string(15) "http version not supported"
*
* @TODO    check terminal mode
*/

final class commandServer extends controller
{
    public function run()
    {
    	$cached = $this->cache('read', 'ssl', $this->domain);
        if (is_string($cached) !== true) {
        	if ($this->arguments(0) !== true) {
        		return $this->returnResponse(200);
        	}
            $validated = $this->ssl();
            if (is_string($validated) === true) {
                $this->cache('writte', 'ssl', $this->domain, (string) $validated);
                return $this->returnResponse(200);
            }
        }
        return $this->returnResponse($this->error->noValidSsl());
    }

    private function ssl($checkDomain = false, $selfsigned = false, $timeout = 1, $checked = false)
    {
        $verifyPeer =($selfsigned === true) ? false : true;
        $domain = (is_string($checkDomain) === true) ? $this->sanitize->domain($checkDomain) : $this->domain;
        $this->debug('domain: ' . $domain);
        $streamContext = stream_context_create([
        'ssl' => [
            'capture_peer_cert' => true,
            ],
        ]);
        try {
            $client = stream_socket_client(
                "ssl://" . $domain . ":443",
                $errorNumber,
                $errorDescription,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $streamContext
            );
            if (is_resource($client) === true) {
                $response = stream_context_get_params($client);
                $certificateProperties = openssl_x509_parse($response['options']['ssl']['peer_certificate']);
            } else {
                $this->debug('[SSL] no valid resource');
            }
        } catch(\PDOException $e) {
            $certificateProperties = false;
        }
        if (isset($certificateProperties['validFrom_time_t']) === true && isset($certificateProperties['validTo_time_t']) === true) {
            if (time(true) > $certificateProperties['validFrom_time_t'] && time(true) < $certificateProperties['validTo_time_t']) {
                $this->debug('[SSL] validated');
                return $certificateProperties['name'];
            }
        }
        $this->warning('[SSL] unvalid');
        return false;
    }


}
