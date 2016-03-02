<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Quickpay Complete Capture Request
 */
class CompleteCaptureRequest extends CompletePurchaseRequest
{
	public function sendData($data)
	{
		return $this->response = new CompleteCaptureResponse($this, $data);
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
