<?php

namespace Omnipay\Quickpay\Message;

/**
 * Look up a payment or subscription.
 *
 * GET /{payments|subscriptions}/{id}
 */
class FetchTransactionRequest extends AbstractRequest
{
    protected $responseClass = FetchTransactionResponse::class;

    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setHttpMethod('GET');
    }

    public function getUrl()
    {
        return $this->buildUrl([$this->getTypeOfRequest(), $this->getTransactionReference()], false);
    }

    public function getData()
    {
        $this->validate('transactionReference');
        return null;
    }
}
