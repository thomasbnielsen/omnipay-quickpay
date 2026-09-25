<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Notify Request: same as CompleteRequest, returns a NotifyResponse.
 */
class NotifyRequest extends CompleteRequest
{
    public function sendData($data)
    {
        return $this->response = new NotifyResponse($this, $data);
    }
}
