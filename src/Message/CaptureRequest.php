<?php

namespace Omnipay\Quickpay\Message;

/**
 * POST /payments/{id}/capture[?synchronized]  body: {amount}
 */
class CaptureRequest extends AbstractRequest
{
    protected $responseClass = CaptureResponse::class;

    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setApiMethod('capture');
    }

    public function getData()
    {
        $this->validate('transactionReference', 'amount');
        return parent::getData();
    }
}
