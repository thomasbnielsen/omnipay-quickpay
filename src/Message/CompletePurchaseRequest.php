<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Quickpay Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {

        return $this->httpRequest->query->all();
    }


    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
        return $this->sendData($this->getData());
    }
}
