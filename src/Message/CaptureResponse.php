<?php

namespace Omnipay\Quickpay\Message;

class CaptureResponse extends Response
{
	// return the response body for the app to use
	public function isSuccessful()
	{
		if($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
			$data = end($response_body->operations);
			if ($data->qp_status_code=="20000") {
				return true;
			}
		}
		return false;
	}
} 