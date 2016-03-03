<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

	protected $endpoint = 'https://api.quickpay.net/';

	private $apimethod = 'capture';

	public function getData()
	{
		$data = array(
			'id' => $this->getTransactionReference(),
			'amount' => $this->getAmountInteger()
		);
		return $data;
	}

	public function getHttpMethod()
	{
		return 'POST';
	}

	public function sendData($data)
	{
		$httpRequest = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->getEndPoint() . 'payments/' . $this->getTransactionReference() . '/' . $this->getApiMethod() . '?synchronized',
			null,
			$data
		)->setHeader('Authorization', ' Basic '. base64_encode(":" . $this->getApiKey()))
			->setHeader('Accept-Version', ' v10')
			->setHeader('QuickPay-Callback-Url', $this->getNotifyUrl());

		return $httpRequest->send();
	}

	public function send()
	{
		return $this->sendData($this->getData());
	}

	public function setApiMethod($value){
		return $this->apimethod = $value;
	}

	public function getApiMethod(){
		return $this->apimethod;
	}

	public function getEndPoint(){
		return $this->endpoint;
	}

	public function getMerchant()
	{
		return $this->getParameter('merchant');
	}

	public function setMerchant($value)
	{
		return $this->setParameter('merchant', $value);
	}

	public function setPrivatekey($value)
	{
		return $this->setParameter('privatekey', $value);
	}

	public function getPrivatekey()
	{
		return $this->getParameter('privatekey');
	}

	public function getAgreement()
	{
		return $this->getParameter('agreement');
	}

	public function setAgreement($value)
	{
		return $this->setParameter('agreement', $value);
	}

	public function getPaymentWindowAgreement()
	{
		return $this->getParameter('payment_window_agreement');
	}

	public function setPaymentWindowAgreement($value)
	{
		return $this->setParameter('payment_window_agreement', $value);
	}

	public function setApikey($value)
	{
		return $this->setParameter('apikey', $value);
	}

	public function getApikey()
	{
		return $this->getParameter('apikey');
	}

	public function setPaymentWindowApikey($value)
	{
		return $this->setParameter('payment_window_apikey', $value);
	}

	public function getPaymentWindowApikey()
	{
		return $this->getParameter('payment_window_apikey');
	}

	public function getLanguage()
	{
		return $this->getParameter('language');
	}

	public function setLanguage($value)
	{
		return $this->setParameter('language', $value);
	}

	public function getPaymentMethods(){
		return $this->getParameter('payment_methods');
	}

	public function setPaymentMethods($value = array()){
		return $this->setParameter('payment_methods', $value);
	}
}
