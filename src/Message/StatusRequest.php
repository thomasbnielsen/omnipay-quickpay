<?php


namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class StatusRequest extends AbstractRequest
{
 
    public function __construct($httpClient, $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->setHttpMethod('GET');
    }

    public function send()
    {
        $reference = $this->getTransactionReference();
        $reference = '';

        $url  = $this->getEndPoint() . '/' . $this->getTypeOfRequest();
        if ($reference) {
            $url .= '/' . $reference;
        }
        $httpResponse = $this->httpClient->request('GET', $url, [
			'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
			'Accept-Version' => 'v10',
			'Content-Type' => 'application/json',
			'QuickPay-Callback-Url' => $this->getNotifyUrl()
		], null);

        $body = $httpResponse->getBody()->getContents();

        return new StatusResponse($this, $body, $reference);
    }
}
