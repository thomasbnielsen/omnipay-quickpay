<?php

namespace Omnipay\Quickpay\Message;

/**
 * completePurchase / completeAuthorize.
 *
 * - Quickpay callback (notify URL): the checksum-verified JSON body is the result.
 *   No API call is made.
 * - Browser return (continue URL): there is no result in the request; the response is
 *   not successful and the application waits for the callback.
 */
class CompleteRequest extends AbstractRequest
{
    /**
     * @return string|array Raw callback body, or the query parameters on a browser return
     */
    public function getData()
    {
        if (CallbackVerifier::isCallback($this->httpRequest)) {
            return CallbackVerifier::verify($this->httpRequest, $this->getPrivateKey());
        }

        return $this->httpRequest->query->all();
    }

    public function sendData($data)
    {
        return $this->response = new Response($this, $data);
    }
}
