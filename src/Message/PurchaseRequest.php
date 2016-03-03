<?php

namespace Omnipay\Quickpay\Message;


/**
 * quickpay Purchase Request
 * Docs can be found at http://tech.quickpay.net/payments/form/
 */
class PurchaseRequest extends AbstractRequest
{
	protected $endpoint = 'https://payment.quickpay.net';

	public function getQuickpayParams()
	{
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
			"autocapture" => 1,
			"payment_methods" => $this->getPaymentMethods()
		);

		return $params;
	}

	public function getData()
	{
		// checks if any of these are empty, so we can throw an error without calling the API
		$this->validate('merchant', 'agreement', 'amount', 'transactionId');

		return $this->createChecksum($this->getQuickpayParams());
	}

	public function createChecksum($data)
	{
		$data["checksum"] = $this->sign($data, $this->getPaymentWindowApikey());
		return $data;
	}

	// taken from quickpays PHP example on how to calculate checksum for the payment form
	public function sign($params, $api_key)
	{
		$flattened_params = $this->flatten_params($params);
		ksort($flattened_params);
		$base = implode(" ", $flattened_params);

		return hash_hmac("sha256", $base, $api_key);
	}

	public function flatten_params($obj, $result = array(), $path = array())
	{
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

	public function getHttpMethod(){}

}
