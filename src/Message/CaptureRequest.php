<?php

// TODO

namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CaptureRequest extends PurchaseRequest
{
    protected $endpoint = 'https://api.quickpay.net';
// currently not working

    public function getData()
    {
		$data = parent::getData();

        return $data;
    }

    /**
     * @param mixed $data
     * @return CaptureResponse
     */
	public function sendData($data)
	{
		return $this->response = new CaptureResponse($this, $data);
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

}
