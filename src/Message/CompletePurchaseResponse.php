<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

	public function __construct($request, $data)
	{
		$this->request = $request;
		$this->data = $data;
	}


    public function isSuccessful()
    {
		$status = $this->getCode();
		// should check for valid codes in headers, not just return true
		//if ($status == 200) {
		//	return true;
		//}
		return true;
    }

	public function getCode(){
		return isset($this->request['operations']['qp_status_code']) ? $this->request['operations']['qp_status_code'] : '';
	}

	public function getTransactionReference()
	{
		return isset($this->data['order_id']) ? $this->data['order_id'] : '';
	}

}
