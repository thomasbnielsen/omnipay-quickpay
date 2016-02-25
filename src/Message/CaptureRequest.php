<?php

// TODO

namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CaptureRequest extends PurchaseRequest
{
// currently not working
	protected $endpoint = 'http://api.quickpay.net';

    public function getData()
    {
		$data = parent::getData();
        return $data;
    }

	public function getHttpMethod(){
		return 'POST';
	}

    /**
     * @param mixed $data
     * @return CaptureResponse
     */
	public function sendData($data)
	{

		// modifying the request taken from Stripe driver
		$httpRequest = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->getEndpoint(),
			null,
			$data
		);

		$httpResponse = $httpRequest
			->setHeader('Authorization', 'Basic '.base64_encode($this->getApiKey().':'))
			->setHeader('id', $this->getTransactionReference())
			->setHeader('amount', $this->getAmount())
			->send();

		return $this->response = new CaptureResponse($this, $httpResponse);
	}


    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
        return $this->sendData($this->getData());
    }

	public function getEndpoint()
	{
		// old
		//return "https://secure.quickpay.dk/form/";
		return $this->endpoint;
	}

}
