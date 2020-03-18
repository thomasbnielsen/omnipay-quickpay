<?php


namespace Omnipay\Quickpay\Message;

/**
 * quickpay Refund Request
 */
class RefundRequest extends AbstractRequest
{

    public function send()
    {
        $reference = $this->getTransactionReference();

        $url  = $this->getEndPoint() . $this->getTypeOfRequest() . '/' .$reference . '/refund';

        $requestParams = [
			'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
			'Accept-Version' => 'v10',
			'Content-Type' => 'application/json',
			'QuickPay-Callback-Url' => $this->getNotifyUrl(),
			'id' => $reference,
			'amount' => $this->getAmountInteger()
		];

         $httpResponse = $this->httpClient->request('POST', $url, $requestParams, json_encode(['id' => $reference, 'amount' => $this->getAmountInteger()]));

        $body = $httpResponse->getBody()->getContents();

        return new RefundResponse($this, $body, $reference);
    }

}
