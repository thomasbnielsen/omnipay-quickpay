<?php

namespace Omnipay\Quickpay\Message;

/**
 * Successful only when the latest refund operation is approved; pending while Quickpay processes it.
 */
class RefundResponse extends Response
{
    protected $expectedOperation = 'refund';
}
