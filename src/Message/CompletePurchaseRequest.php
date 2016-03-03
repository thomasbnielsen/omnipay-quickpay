<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Complete Purchase Request
 */
class CompletePurchaseRequest extends CompleteRequest
{
	public function sendData($data)
	{
		return $this->response = new CompletePurchaseResponse($this, $data);
	}
}
