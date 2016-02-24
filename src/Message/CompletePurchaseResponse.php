<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;
// in this response we should get some data from the gateway, to make sure of the status of the payment,
// here we should also get acces to getTransactionReference() which should be stored in the projects DB, as a reference point to later capture the payment
class CompletePurchaseResponse extends AbstractResponse
{
/*
 *  is called on main class
	public function __construct($request, $data)
	{
		$this->request = $request;
		$this->data = $data;
	}

*/
    public function isSuccessful()
    {
		//$status = $this->getCode();
		// should check for valid codes in headers, not just return true
		//if ($status == 200) {
		//	return true;
		//}

		//if($this->data['checksum'] == $this->request['Quickpay-Checksum-Sha256']){
		//	return true
		//}
		return true;
    }

	//public function getCode(){
	//	return isset($this->request['operations']['qp_status_code']) ? $this->request['operations']['qp_status_code'] : '';
	//}

	public function getTransactionReference()
	{
		$input = json_decode($this->request);

		// store this on merchant sites db!
		//return isset($input->id) ? $input->id : '';
		return 'order123';
	}

}
