<?php

namespace Omnipay\Quickpay\Message;


/**
 * quickpay Purchase Request
 * Docs can be found at http://tech.quickpay.net/payments/form/
 */
class PurchaseRequest extends AbstractRequest
{
	public function getQuickpayParams(){
		$params = array(
			"version"      => "v10",
			"merchant_id"  => $this->getMerchant(),
			"agreement_id" => $this->getPaymentWindowAgreement(),
			"order_id"     => "orderID" . $this->getTransactionId(),
			"amount"       => $this->getAmountInteger(),
			"currency"     => $this->getCurrency(),
			"continueurl" => $this->getReturnUrl(),
			"cancelurl"   => $this->getCancelUrl(),
			"callbackurl" => $this->getNotifyUrl(),
			"language" => $this->getLanguage(),
			"autocapture" => 0,
			"payment_methods" => $this->getPaymentMethods()
		);

		return $params;
	}

	public function getData()
	{
		// checks if any of these are empty, so we can throw an error without calling the API
		$this->validate('merchant', 'agreement', 'amount', 'transactionId');

		/*
		$data['version'] = "v10";
		$data['merchant_id'] = $this->getMerchant();
		$data['agreement_id'] = $this->getPaymentWindowAgreement();
		// quickpay requires order numbers to be at least 4 characters long
		$data['order_id'] = "orderID" . $this->getTransactionId();
 		$data['amount'] = $this->getAmountInteger();
 		$data['currency'] = $this->getCurrency();
		$data['cancelurl'] = $this->getCancelUrl();
		$data['callbackurl'] = $this->getNotifyUrl();
		//$data['callbackurl'] = 'http://requestb.in/1h5j3h51';
		// specify redirect url here, after payment
		$data['continueurl'] = $this->getReturnUrl();
		// set language of payment window
		$data['language'] = $this->getLanguage(); 
		// if set to 1, will autocapture
		$data['autocapture'] = 0;
		// limit payment methods by setting this
		$data['payment_methods'] = $this->getPaymentMethods();
		*/

		return $this->createChecksum($this->getQuickpayParams());
	}
	/*
		public function form_fields($input_data)
		{

			$valid_input_ordered = array('version', 'merchant_id', 'agreement_id', 'order_id', 'amount', 'currency', 'cancelurl', 'continueurl','callbackurl','autocapture', 'payment_methods', 'language');
			// create array with our data
			foreach($valid_input_ordered as $key)
			{
				if(isset($input_data[$key]))
				{
					$data_fields[$key] = $input_data[$key];
				}
			}
			//create quickpay checksum
			// api key is used
			// this used to be called md5secret in the old quickpay platform
			$data_fields['checksum'] = $this->sign($data_fields, $this->getPaymentWindowApikey());
			return $data_fields;

		}
	*/

	public function createChecksum($data){
		$data["checksum"] = $this->sign($data, $this->getPaymentWindowApikey());
		return $data;
	}
	// taken from quickpays PHP example on how to calculate checksum for the payment form
	public function sign($params, $api_key) {
		$flattened_params = $this->flatten_params($params);
		ksort($flattened_params);
		$base = implode(" ", $flattened_params);

		return hash_hmac("sha256", $base, $api_key);
	}

	public function flatten_params($obj, $result = array(), $path = array()) {
		if (is_array($obj)) {
			foreach ($obj as $k => $v) {
				$result = array_merge($result, $this->flatten_params($v, $result, array_merge($path, array($k))));
			}
		} else {
			$result[implode("", array_map(function($p) { return "[{$p}]"; }, $path))] = $obj;
		}

		return $result;
	}


	public function sendData($data)
	{
		return $this->response = new PurchaseResponse($this, $data);
	}

	/**
	 * Send the request
	 *
	 * @return ResponseInterface
	 */
	public function send()
	{
		return $this->sendData($this->getData());
	}

	public function getEndpoint()
	{
		// old
		//return "https://secure.quickpay.dk/form/";
		return 'https://payment.quickpay.net';
	}

}
