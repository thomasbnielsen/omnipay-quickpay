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
		// FIXME: Most messages to gateways are POST, so this should be the default.
		// Only over-ride it to GET where you need to.
		return 'POST';
	}

    /**
     * @param mixed $data
     * @return CaptureResponse
     */
	public function sendData($data)
	{
		// FIXME: Probably a better way of doing this is to move this into the parent
		// AbstractRequest class.  Then you just need each message class to implement
		// getData().  The send process should mostly be the same for each message
		// with the possible exception of getData() and getEndpoint() and for those
		// messages that are not POST then also getHttpMethod()

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
