<?php

namespace Omnipay\Quickpay\Message;

/**
 * Cancel a payment or a subscription (saved card).
 *
 * POST /{payments|subscriptions}/{id}/cancel[?synchronized]
 * Use type=subscription to cancel a saved card. Quickpay does not support DELETE on subscriptions.
 */
class VoidRequest extends AbstractRequest
{
    protected $responseClass = VoidResponse::class;

    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setApiMethod('cancel');
    }

    public function getData()
    {
        $this->validate('transactionReference');
        return [];
    }
}
