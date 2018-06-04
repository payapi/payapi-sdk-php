<?php

namespace payapi;

//-> @TODO this should fetch metadata from product url (passed in query)
//-> param ¢_GET['mode']
//-> param ¢_GET['product']

final class commandDemo extends controller
{

    private $returning   = true;
    private $partial     = false;
    private $modes       = array(
        "confirm",
        "success",
        "failed",
        "cancel"
    );
    private $return      = array(
        "success",
        "failed",
        "canceled",
        "unavailable"
    );
    private $product     = false;
    private $urlProduct  = false;
    private $urlBase     = false;
    private $metadata    = false;
    private $encodedMode = array();
    private $mode        = 'load';
    private $monetary    = false;
    private $branding    = array();
    private $frontend    = null;

    public function run()
    {
        //die($this->api->request->url()); // https://www.oc23.dev/index.php?route=payapi/test&mode=confirm
        $this->encodedMode();
        //-> this should be fetched from metadata
        $this->language = $this->adaptor->language();
        $this->currency = $this->adaptor->currency();
        //->
        $this->wording->load('demo');
        $this->monetary = $this->wording->get('monetary');
        $this->config->set('monetary', $this->monetary);
        //die($this->urlBase());
        //-> @TODO filter arguments
        if ($this->arguments(0) !== $this->serialize->undefined() && is_string($this->arguments(0)) === true) {
            $this->branding = $this->pluginBranding($this->sanitize->string($this->arguments(0)));
        } else {
            $this->branding = $this->pluginBranding();
        }
        $title = $this->branding['partnerName'] . ', ' . $this->branding['partnerSlogan'];
        //-> @NOTE overwrittes default brand if a new one is requested
        $this->wording->set('branding', $this->branding);
        //-> frontend
        $this->frontend = new frontend($this->language);
        $this->request();
        if ($this->mode !== 'load') {
            $title = $this->wording->get('head_' . $this->mode . '_title') . '. ' . $title;
        }
        $this->wording->set('media', 'https://input.payapi.io/');
        $this->wording->set('payapi_public_id', $this->publicId());
        $this->wording->set('head_title', $title);
        $this->wording->set('language', $this->language);
        $this->wording->set('currency', '<span class="currency">' . $this->currency . '</span>');
        return $this->frontend->render($this->mode);
    }

    private function urlBase()
    {
        $requested = $this->api->request->url();
        if (is_string($requested) === true) {
            $parsed = $this->serialize->urlGet($requested);
            parse_str($this->serialize->urlGet($requested, 'query'), $this->query);
            if (isset($this->query['mode'])) {
                $clean = str_replace(
                    array(
                        '&mode=' . $this->query['mode'],
                        '&amp;mode=' . $this->query['mode'],
                        '?mode=' . $this->query['mode']
                    ),
                    null,
                    $requested
                );
                return $clean;
            } else {
                return $requested;
            }
        }
        return null;
    }

    private function monetize($price)
    {
        return $this->serialize->monetize($price, $this->currency, $this->monetary);
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
                    //->
                } elseif (md5($this->api->request->get('mode')) == md5('success') ||
                    md5($this->api->request->get('mode')) == md5('cancel') ||
                    md5($this->api->request->get('mode')) == md5('failed') ||
                    md5($this->api->request->get('mode')) == md5('unavailable')) {
                    $this->mode = 'notice';
                    $this->wording->load('notice');
                    //->
                    $this->wording->set('redirectLocation', '/');
                    $this->wording->set(
                        'title',
                        $this->wording->get('return_' . $this->api->request->get('mode') . '_title')
                    );
                    $this->wording->set(
                        'description',
                        $this->wording->get('return_' . $this->api->request->get('mode') . '_info')
                    );
                    $this->wording->set(
                        'class',
                        $this->wording->get('return_' . $this->api->request->get('mode') . '_class')
                    );
                } else {
                    //$rediredt = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '?' . 'mode' . '=' . 'confirm';
                    $redirect = 'https://www.oc23.dev/index.php?route=payapi/test' . '&' . 'mode' . '=' . 'confirm' . '&product=' . urlencode($this->product['url']);
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
        //-> @TOREVIEW
        if (md5($this->api->request->get('isPartial')) === md5('1')) {
            $this->partial = true;
            $payment = 'partial';
            $this->wording->set('text_partial_notice_1', sprintf('1000', $this->wording->get('text_partial_notice_1')));
            $this->wording->set('text_partial_notice_2', sprintf('1000', $this->wording->get('text_partial_notice_2')));
        } else {
            $payment = 'default';
        }
        $this->wording->set('paymentBLock', $this->frontend->view('payment/' . $payment . '.payment', false));
        $this->wording->set('paymentInfo', $this->frontend->view('payment/' . $payment . '.info', false));
        return $this->partial;
    }

    private function metadata($url)
    {
        if (is_string($url) === true) {
            //var_dump($url); exit;
            $metadata = get_meta_tags($url);
            $validated = $this->validate->schema($metadata, $this->load->schema('metadata'));
            if (is_array($validated) === true) {
                $this->metadata = $validated;
                return $this->metadata;
            }
            return false;
        }

        return false;
    }

    private function product()
    {
        //->
        if (is_string($this->api->request->get('product')) === true) {
            $this->urlProduct = $this->api->request->get('product');
            if ($this->metadata($this->urlProduct) !== false) {
                if (isset($this->metadata['order_shippinghandlingfeeincentsincvat']) === true) {
                    $shipping = $this->metadata['order_shippinghandlingfeeincentsincvat']/100;
                } else {
                    $shipping = 0;
                }
                
                $this->product = array(
                    "id"            => $this->metadata['product_id'],
                    "name"          => $this->metadata['product_title'],
                    "price"         => $this->metadata['product_priceincentsincvat']/100,
                    "price_html"    => $this->monetize($this->metadata['product_priceincentsincvat']/100),
                    "quantity"      => $this->metadata['product_quantity'],
                    "image"         => urldecode($this->metadata['product_imageurl']),
                    "shipping"      => $shipping,
                    "shipping_html" => $this->monetize($shipping),
                    "url"           => $this->urlProduct
                );
                //-> partial
                if (isset($this->metadata['order_preselectedPartialPayment']) &&
                    $this->metadata['order_preselectedPartialPayment'] != null) {
                    //-> enable partial
                    $totalPrice = ($this->product['price'] + $this->product['shipping']);
                    $this->product['partial'] = $this->calculatePartialPayment(
                        $totalPrice,
                        $this->currency,
                        $this->localized['countryCode']
                    );
                }
                return $this->product;
            }
        }
        return false;
    }

    private function payment()
    {
        $total = $this->product['price'] + $this->product['shipping'];
        $total_html = $this->monetize($total);
        return array(
            "total"         => $total,
            "total_html"    => $total_html,
            "method"        => 'VISA',
            "card"          => '**** **** **** *123'
        );
    }

    private function consumer()
    {
        $name = $this->wording->get('customer_name');
        $part = explode(' ', $name);
        return array(
            "name"       => $part[0],
            "surname"    => $part[1],
            "fullname"   => $name,
            "email"      => $this->wording->get('customer_email'),
            "address_1"  => $this->wording->get('customer_address_1'),
            "address_2"  => $this->wording->get('customer_address_2'),
            "method"     => $this->wording->get('customer_method'),
            "country"    => $this->wording->get('customer_country')
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
        if (isset($_GET['product']) === true && is_string($_GET['product']) === true &&
            substr_count($_GET['product'], '.') === 2) {
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
        foreach ($this->modes as $key => $value) {
            $encoded = md5($value);
            $this->encodedMode[$encoded] = $value;
        }
        return $this->encodedMode;
    }
}
