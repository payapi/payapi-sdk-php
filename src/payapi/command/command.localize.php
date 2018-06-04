<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->localize()
*           $sdk->localize(true) //-> refresh localization
*           $sdk->localize(true/false, $ip) //-> localizates ip
*
* @TYPE     public
*
* @PARAMS
*           $ip = valid ip
*
* @RETURNS
*           localization array OR $this->error->notFound()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(8) {
*            ["ip"]=>
*             string(12) "84.79.234.58"
*            ["countryCode"]=>
*             string(2) "ES"
*            ["countryName"]=>
*             string(5) "Spain"
*            ["regionName"]=>
*             string(6) "Madrid"
*            ["regionCode"]=>
*             string(2) "MD"
*            ["postalCode"]=>
*             string(5) "28529"
*            ["timezone"]=>
*             string(13) "Europe/Madrid"
*            ["timestamp"]=>
*             float(1500884755.4039)
*           }
*
* @NOTE
*          this command is NOT terminal mode compatible
*          localization info is cached
*          data is adapted through plugin
*
* @VALID
*          schema.localize
*
* @TODO
*          localize just when needed->transaction
*          common ip cache is still isolated at encoding
*
*/

final class commandLocalize extends controller
{

    public function run()
    {
        if ($this->api->env() !== 'server' && $this->arguments(1) === $this->serialize->undefined()) {
            return $this->returnResponse($this->error->notFound());
        }
        if ($this->arguments(1) != $this->serialize->undefined()) {
            if ($this->validate->ip($this->arguments(1)) === true) {
                $ip = $this->arguments(1);
            } else {
                return $this->returnResponse($this->error->badRequest());
            }
        } else {
            $ip = $this->ip();
            if ($this->arguments(0) !== $this->serialize->undefined() && $this->arguments(0) !== true) {
                if (is_array($this->localized) === true) {
                    return $this->render($this->localized);
                } else {
                    return $this->returnResponse($this->error->notValidLocalizationSchema());
                }
            }
        }

        if ($this->validate->ip($ip) === true) {
            $this->debug('[check] ' . $ip);
            $cached = $this->cache('read', 'localize', $ip);
            if ($this->arguments(0) !== $this->serialize->undefined() && $this->arguments(0) !== true && $cached !== false) {
                return $this->render($cached);
            } else {
                $endPoint = $this->serialize->endPointLocalization($ip);
                $request = $this->curl($endPoint, false, false);
                if ($request !== false && isset($request['code']) === true) {
                    if ($request['code'] === 200) {
                        $validated = $this->validate->schema($request['data'], $this->load->schema('localize'));
                        if (is_array($validated) !== false) {
                            $this->debug('[localize] valid schema');
                            $adaptedData = $this->adaptor->localized($validated);
                            $this->cache('writte', 'localize', $ip, $adaptedData);
                            return $this->render($this->cache('read', 'localize', $ip));
                        } else {
                            //-> not valid schema from PA
                            $this->error('no valid localization', 'warning');
                            return $this->returnResponse($this->error->notValidLocalizationSchema());
                        }
                    } else {
                        $this->error('no valid localization', 'warning');
                        return $this->returnResponse($this->error->notValidLocalizationSchema());
                    }
                } else {
                    return $this->returnResponse($this->error->badRequest());
                }
            }
        }
        //->
        return $this->returnResponse($this->error->timeout());
    }
}
