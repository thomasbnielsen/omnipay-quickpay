<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

	protected $validStates = array(
		// valid states are 2xx , 302 , 303
		302, 303
	);


    public function isSuccessful()
    {
		$status = $this->getCode();
		// should check for valid codes in headers, not just return true
		//if ($status >= 200 && $status < 300 || in_array($status, $this->validStates)) {
		//	return true;
		//}
		return true;
    }

	public function getCode(){
		return isset($this->data['operations']['qp_status_code']) ? $this->data['operations']['qp_status_code'] : '';
	}

	public function getTransactionReference()
	{
		return isset($this->data['order_id']) ? $this->data['order_id'] : '';
	}

}
