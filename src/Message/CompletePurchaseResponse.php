<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

	public function isSuccessful()
	{
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
			$data = end($response_body->operations);
			if ($response_body->accepted && $data->type=='authorize' && $data->qp_status_code=="20000") {
				return true;
			}
		}
		// always returns false on returnURL, because Quickpay returnURL get no data
		// it can return true when looking at the data from the notifyURL
		// your app should have a way to handle this, since the callback is also asynchronous!
		return false;
	}

	public function getResponseBody(){
		$response_body = json_decode($this->data);
		if (json_last_error() === JSON_ERROR_NONE) {
			// JSON is valid
			return $response_body;
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
