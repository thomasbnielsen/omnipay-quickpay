<?php

// TODO

namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CaptureRequest extends PurchaseRequest
{
// currently not working
	protected $endpoint = 'https://api.quickpay.net';

	public function getData()
	{
		$data = array();
		$data['id'] = $this->getTransactionReference();
		$data['amount'] = $this->getAmountInteger();
		return http_build_query($data);
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
		// don't throw exceptions for 4xx errors
		/*
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );		
		*/
		$httpRequest = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->getEndpoint(),
			null,
			$data
		);

		$httpResponse = $httpRequest
			->setHeader('Authorization', ' Basic '. base64_encode(":" . $this->getApiKey()))
			->setHeader('Accept-Version', ' v10')
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
		return $this->endpoint;
	}

}
