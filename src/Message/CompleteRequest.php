<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Quickpay Complete Capture Request
 */
class CompleteRequest extends AbstractRequest
{
	public function getData()
	{
		$data = $this->httpRequest->query->all();

		// if its the notifyUrl (callback) being handled and not returnUrl
		if($this->httpRequest->headers->get('Content-Type') == "application/json"){
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

	public function sendData($data)
	{
		return $this->response = new Response($this, $data);
	}

	public function getHttpMethod(){}
}

