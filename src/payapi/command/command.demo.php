<?php

namespace payapi;

final class commandDemo extends controller
{

    private   $returning       =     true;
    private   $modes           =     array(
                  "confirm",
                  "success",
                  "failed",
                  "cancel"
              );
    private   $return          =     array(
                  "success",
                  "failed",
                  "canceled",
                  "unavailable"
              );
    private   $product         =     false;
    private   $encodedMode     =   array();
    private   $mode            =    'load';
    private   $branding        =   array();
    private   $frontend        =      null;

    public function run()
    {
        //var_dump($_SERVER); exit;
        //die($this->api->request->url()); // https://www.oc23.dev/index.php?route=payapi/test&mode=confirm
        $this->encodedMode();
        $this->wording->load('demo');
        //die($this->urlBase());
        setLocale(LC_MONETARY, $this->wording->get('monetary') . '.UTF-8');
        //-> @TODO filter arguments
        if (is_string($this->arguments(0)) === true) {
            $this->branding = $this->pluginBranding($this->arguments(0));
        } else {
            $this->branding = $this->pluginBranding();
        }
        $title = $this->branding['partnerName'] . ', ' . $this->branding['partnerSlogan'];
        //-> @NOTE overwrittes default brand if a new one is requested
        $this->wording->set('branding', $this->branding);
        $this->request();
        if ($this->mode !== 'load') {
            $title = $this->wording->get('head_' . $this->mode . '_title') . '. ' . $title;
        }
        $this->wording->set('media', 'https://input.payapi.io/');
        $this->wording->set('payapi_public_id', $this->publicId());
        $this->wording->set('head_title', $title);
        $this->wording->set('language', $this->language);
        $this->wording->set('currency', '<span class="currency">' . $this->currency . '</span>');
        //-> frontend
        $this->frontend = new frontend($this->adaptor->language());
        return $this->frontend->render($this->mode);
    }

    private function urlBase()
    {
        $requested = $this->api->request->url();
        if (is_string($requested) === true) {
            $parsed = $this->serialize->urlGet($requested);
            parse_str($this->serialize->urlGet($requested, 'query'), $query);
            if(isset($query['mode'])) {
                $clean = str_replace(array('&mode=' . $query['mode'], '&amp;mode=' . $query['mode'], '?mode=' . $query['mode']), null, $requested);
                return $clean;
            } else {
                return $requested;
            }
        }
        return null;
    }

    private function request()
    {
        //-> TODO just for testing, make this dynamic
        $this->product = $this->product();
        $this->wording->set('product', $this->product);
        $this->wording->set('consumer', $this->consumer());
        $this->wording->set('payment', $this->payment());
        if ($this->api->request->shell() === false && $this->api->request->method() === 'get') {
            if (is_string($this->api->request->get('mode')) === true) {
                $parsed = $this->serialize->urlGet($this->api->request->url());
                //var_dump($parsed); exit;

                $this->partialPayment();
                if (md5($this->api->request->get('mode')) == md5('confirm')) {
                    $this->mode = 'confirm';
                } else if (md5($this->api->request->get('mode')) == md5('success') || md5($this->api->request->get('mode')) == md5('cancel') || md5($this->api->request->get('mode')) == md5('failed') || md5($this->api->request->get('mode')) == md5('unavailable')) {
                    $this->mode = 'notice';
                    $this->wording->load('notice');
                    //->
                    $this->wording->set('redirectLocation', '/');
                    $this->wording->set('title', $this->wording->get('return_' . $this->api->request->get('mode') . '_title'));
                    $this->wording->set('description', $this->wording->get('return_' . $this->api->request->get('mode') . '_info'));
                    $this->wording->set('class', $this->wording->get('return_' . $this->api->request->get('mode') . '_class'));
                } else {
                    //$rediredt = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '?' . 'mode' . '=' . 'cponfirm';
                    $redirect = 'https://www.oc23.dev/index.php?route=payapi/test' . '&' . 'mode' . '=' . 'confirm';
                    $this->wording->set('redirectLocation', $redirect);
                }
            }
            $this->wording->set('head_title', $this->wording->get('return_unavailable'));
            return true;
        }
        $this->wording->set('redirectLocation', '/');
        $this->wording->set('head_title', $this->wording->get('return_unavailable'));
        $this->wording->set('title', $this->wording->get('return_unavailable_title'));
        $this->wording->set('description', $this->wording->get('return_unavailable_info'));
        $this->wording->set('class', $this->wording->get('return_unavailable_class'));
        $this->warning('[DEMO] shell access');
        $this->mode = 'unavailable';
    }

    private function partialPayment()
    {
        if (md5($this->api->request->get('isPartial')) === md5('1')) {
            $this->wording->set('isPartial', true);
        } else {
            $this->wording->set('isPartial', false);
        }

        
    }

    private function product()
    {
        $price = 200;
        $shipping = 9;
        $price_html = $this->serialize->monetize($price);
        $shipping_html = $this->serialize->monetize($shipping);
        return array(
            "id"            => 21,
            "name"          => "TEST PRODUCT",
            "price"         => $price,
            "price_html"    => $price_html,
            "quantity"      => 1,
            "image"         => 'https://store.multimerchantshop.xyz/media/983ab1519a8b553ec58125a13bf09471/image/cache/catalog/hp_1-228x228.jpg',
            "shipping"      => $shipping,
            "shipping_html" => $shipping_html,
            "url"           => 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=46'

        );
    }

    private function payment()
    {
        $total = $this->product['price'] + $this->product['shipping'];
        $total_html = $this->serialize->monetize($total);
        return array(
            "total"         => $total,
            "total_html"    => $total_html,
            "method"        => 'VISA',
            "card"          => '**** **** **** *123'
        );
    }

    private function consumer()
    {
        return array(
            "name"     => 'Jonh',
            "surname"  => 'Doe',
            "fullname" => 'Jonh Doe',
            "email"    => 'jonh@doe.com',
            "address"  => 'My address 123',
            "method"   => 'VISA',
            "postal"   => 4321,
            "city"     => 'London',
            "region"   => 'City of London',
            "country"  => 'United Kingdom'
        );
    }


    private function mode()
    {
        if (isset($_GET['mode']) === true && is_string($_GET['mode']) === true) {
            $encoded = md5($_GET['mode']);
            if (in_array($encoded, $this->encodedMode) === true) {
                $this->mode = $this->encodedMode[$encoded];
            } else {
                $this->warning('no valid mode', 'DEMO');
            }
        }
    }

    private function validate()
    {
        //->
        if (isset($_GET['product']) === true && is_string($_GET['product']) === true && substr_count($_GET['product'], '.') === 2) {
            $this->product = $this-decode(addslashes($_GET['product']));
            if (is_string($this->product) === true) {
                return $this->product;
            }
        }
        $this->warning('no valid request', 'DEMO');
        return false;
    }

    private function wording()
    {
        //->
        if (is_string($this->arguments(0)) === true) {
            //-> TODO get language from adaptor
            $this->language = $this->arguments(0);
        } else {
            $this->language = 'en-gb';
        }
    }

    private function encodedMode()
    {
        foreach($this->modes as $key => $value) {
            $encoded = md5($value);
            $this->encodedMode[$encoded] = $value;
        }
        return $this->encodedMode;
    }


}
