<?php

namespace Omnipay\Quickpay\Message;

class CompleteCaptureResponse extends CompletePurchaseResponse
{

	public function isSuccessful()
	{
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
			$data = end($response_body->operations);
			if ($data->qp_status_code=="20000" && ($data->aq_status_code=="000" || $data->aq_status_code=="20000")) {
				return true;
			}
		}

		return false;
	}

	public function getResponseBody(){
		if(is_string($this->data)){
			$response_body = json_decode($this->data);
			if (json_last_error() === JSON_ERROR_NONE) {
				// JSON is valid
				return $response_body;
			}
		}
		return false;
	}

	public function getTransactionReference()
	{
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
		}
		return isset($response_body->id) ? $response_body->id : '';
	}

}
