<?php


namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CaptureRequest extends PurchaseRequest
{
	protected $endpoint = 'https://api.quickpay.net/';

	public function getData()
	{
		$data = array(
			'id' => $this->getTransactionReference(),
			'amount' => $this->getAmountInteger()
		);
		return $data;
	}

	public function getHttpMethod()
	{
		return 'POST';
	}

	public function sendData($data)
	{
		$httpRequest = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->endpoint . 'payments/' . $this->getTransactionReference() . '/capture?synchronized',
			null,
			$data
		)->setHeader('Authorization', ' Basic '. base64_encode(":" . $this->getApiKey()))
			->setHeader('Accept-Version', ' v10')
			->setHeader('QuickPay-Callback-Url', $this->getNotifyUrl());

		return $httpRequest->send();
	}

	public function send()
	{
		return $this->sendData($this->getData());
	}
}
