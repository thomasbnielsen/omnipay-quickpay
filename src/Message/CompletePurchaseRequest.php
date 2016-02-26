<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Quickpay Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
	// parameters that will be included in the $our_checksum
	//protected $signatureParams = array(

	//);

	public function getData()
	{
		$data = $this->httpRequest->query->all();

		// if its the notifyUrl (callback) being handled and not returnUrl
		if($this->httpRequest->headers->get('Quickpay-Checksum-Sha256')){
			$data = json_decode($this->httpRequest->getContent());

			$params = array(
				"version"      => $this->httpRequest->headers->get('Quickpay-Api-Version'),
				"merchant_id"  => $this->getMerchant(),
				"agreement_id" => $this->getPaymentWindowAgreement(),
				"order_id"     => $data->order_id,
				"amount"       => $this->getAmountInteger(),
				"currency"     => $this->getCurrency(),
				"continueurl" => $this->getReturnUrl(),
				"cancelurl"   => $this->getCancelUrl(),
				"callbackurl" => $this->getNotifyUrl(),
				"language" => $this->getLanguage(),
				"autocapture" => 0,
				"payment_methods" => $this->getPaymentMethods()
			);
			$header_checksum = $this->httpRequest->headers->get('Quickpay-Checksum-Sha256');
			$our_checksum = $this->sign($params, $this->getPaymentWindowApikey());

			//mail('sander@nobrainer.dk', 'Test', 'checksum from header: ' . $header_checksum . ' checksum we made: ' . $our_checksum);
			if ($our_checksum != $header_checksum) {
				throw new InvalidResponseException;
			}
		}
		return $data;
	}


	public function sendData($data)
	{
		return $this->response = new CompletePurchaseResponse($this, $data);
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
}
