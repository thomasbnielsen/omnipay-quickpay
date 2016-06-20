<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Notify Request
 */
class NotifyRequest extends CompleteRequest
{

	public function sendData($data)
	{
		return $this->response = new NotifyResponse($this, $data);
	}

	public function getHttpMethod(){}
}

