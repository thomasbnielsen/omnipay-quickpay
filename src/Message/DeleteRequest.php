<?php

namespace Omnipay\Quickpay\Message;

/**
 * Plain resource deletion — DELETE {endpoint}/{payments|subscriptions}/{id},
 * routed by the caller's `type` parameter same as every other subscription-
 * aware request in this driver (see AbstractRequest::getTypeOfRequest()).
 * apiMethod is left empty so AbstractRequest::getUrl() doesn't append a
 * trailing segment.
 */
class DeleteRequest extends AbstractRequest
{
    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setHttpMethod('DELETE');
        $this->setApiMethod('');
    }

    public function getData()
    {
        $this->validate('apikey', 'transactionReference');

        return [];
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getUrl(),
            [
                'Authorization' => 'Basic ' . base64_encode(':' . $this->getApikey()),
                'Accept-Version' => 'v10',
            ]
        );

        return $this->response = new DeleteResponse($this, $httpResponse->getBody()->getContents());
    }
}
