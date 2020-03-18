<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Quickpay Complete Request
 * It is used to check data from a callback and send the json body onwards
 */
class CompleteRequest extends AbstractRequest
{
    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setApiMethod('');
        $this->setHttpMethod('GET');
    }

    public function getData()
    {
        $data = $this->httpRequest->query->all();

        // if its the notifyUrl (callback) being handled and not returnUrl
        if ($this->httpRequest->headers->get('Content-Type') == "application/json") {
            $data = $this->httpRequest->getContent();

            $header_checksum = $this->httpRequest->headers->get('Quickpay-Checksum-Sha256');
            // validate with accounts private key.
            $our_checksum = hash_hmac("sha256", $this->httpRequest->getContent(), $this->getPrivateKey());
            if ($our_checksum != $header_checksum) {
                throw new InvalidResponseException;
            }
        }

        return $data;
    }

    /**
     * @codeCoverageIgnore
     */
    public function sendData($data)
    {
        $httpResponse =  $this->httpClient->request('get', $this->getEndPoint() . '/payments/' . $this->getTransactionReference(),
            [
                'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
                'Accept-Version' => 'v10',
                'Content-Type' => 'application/json',
            ]
        );
        return $this->response = new Response($this, $httpResponse->getBody()->getContents());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHttpMethod()
    {
    }
}

