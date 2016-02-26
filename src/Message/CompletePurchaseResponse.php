<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

	public function __construct($request, $data)
	{
		$this->request = $request;
		// is json object
		$this->data = $data;
	}


	public function isSuccessful()
	{
		$status = $this->getCode();
		if ($status == 20000) {
			return true;
		}
		// always returns false on returnURL, because Quickpay returnURL get no data
		// it can return true when looking at the data from the notifyURL
		// your app should have a way to handle this, since the callback is also asynchronous!
		return false;
	}

	public function getCode(){
		return isset($this->data->qp_status_code) ? $this->data->qp_status_code : '';
	}

	public function getTransactionReference()
	{

		// store this on merchant sites db!
		return isset($this->data->id) ? $this->data->id : '';
		//return '123123123123';
	}

}
