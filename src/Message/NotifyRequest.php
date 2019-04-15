<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Notify Request
 */
class NotifyRequest extends CompleteRequest
{

	public function sendData($data): Response
	{
		return $this->response = new NotifyResponse($this, $data);
	}
}

