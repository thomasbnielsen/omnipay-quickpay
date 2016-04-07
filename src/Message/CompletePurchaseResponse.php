<?php

namespace Omnipay\Quickpay\Message;

class CompletePurchaseResponse extends Response
{

	public function isSuccessful()
	{
		if($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
			$data = end($response_body->operations);
			if ($response_body->accepted && $data->type=='authorize' && $data->qp_status_code=="20000" && $response_body->test_mode != true) {
				return true;
			}
		}

		// always returns false on returnURL, because Quickpay returnURL get no data
		// it can return true when looking at the data from the notifyURL
		// your app should have a way to handle this, since the callback is also asynchronous!
		return false;
	}

}
