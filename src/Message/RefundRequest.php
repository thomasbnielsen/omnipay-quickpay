<?php

namespace Omnipay\Quickpay\Message;

/**
 * POST /payments/{id}/refund[?synchronized]  body: {amount}
 */
class RefundRequest extends AbstractRequest
{
    protected $responseClass = RefundResponse::class;

    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setApiMethod('refund');
    }

    public function getData()
    {
        $this->validate('transactionReference', 'amount');
        return parent::getData();
    }
}
