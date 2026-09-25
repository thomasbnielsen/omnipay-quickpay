<?php

namespace Omnipay\Quickpay\Message;

/**
 * Successful when the resource was found. Use getState(), isAccepted(),
 * getLatestOperation() and the card accessors to inspect it.
 */
class FetchTransactionResponse extends Response
{
    public function isSuccessful()
    {
        return $this->isHttpOk() && $this->hasResourceId();
    }

    public function isPending()
    {
        return false;
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return (string) $this->getState();
        }
        return parent::getMessage();
    }
}
