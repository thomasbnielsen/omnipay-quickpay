<?php

namespace Omnipay\Quickpay\Message;

/**
 * Charge a saved card (subscription).
 *
 * POST /subscriptions/{id}/recurring[?synchronized]  body: {amount, order_id, auto_capture}
 *
 * - transactionReference: the subscription id
 * - orderId (or transactionId): a unique Quickpay order_id for the new payment
 * - auto_capture: defaults to true
 *
 * The response describes the new payment; getTransactionReference() is its id.
 */
class RecurringRequest extends AbstractRequest
{
    protected $responseClass = RecurringResponse::class;

    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setApiMethod('recurring');
    }

    public function getTypeOfRequest()
    {
        return 'subscriptions';
    }

    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $orderId = $this->getOrderID() ?: $this->getTransactionId();
        if ($orderId === null || $orderId === '') {
            throw new \Omnipay\Common\Exception\InvalidRequestException('The orderId (or transactionId) parameter is required');
        }

        $autoCapture = $this->getAutoCapture();

        return [
            'amount' => $this->getAmountInteger(),
            'order_id' => (string) $orderId,
            'auto_capture' => $autoCapture === null ? true : (bool) $autoCapture,
        ];
    }
}
