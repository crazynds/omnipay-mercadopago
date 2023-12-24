<?php

namespace Omnipay\MercadoPago\Message;

use Omnipay\Common\Message\AbstractResponse;

use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }


    public function isSuccessful()
    {
        return !$this->response['error'];
    }

    /**
     * Redirect for the Payment URL
     * @return boolean
     */
    public function isRedirect()
    {
        return false;
    }

    public function getMessage()
    {
        return $this->response['msg'];
    }
}
