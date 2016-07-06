<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

	/**
	 * @var string
	 */
	protected $endpoint = 'https://api.quickpay.net/';

	/**
	 * @var string
	 */
	private $apimethod = 'capture';

	/**
	 * @return array
	 */
	public function getData()
	{
		$data = array(
			'id' => $this->getTransactionReference(),
			'amount' => $this->getAmountInteger()
		);
		return $data;
	}

	/**
	 * @return string
	 */
	public function getHttpMethod()
	{
		return 'POST';
	}

	/**
	 * @param $data
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function send()
	{
		return $this->sendData($this->getData());
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setApiMethod($value){
		return $this->apimethod = $value;
	}

	/**
	 * @return string
	 */
	public function getApiMethod(){
		return $this->apimethod;
	}

	/**
	 * @return string
	 */
	public function getEndPoint(){
		return $this->endpoint;
	}

	/**
	 * @return mixed
	 */
	public function getMerchant()
	{
		return $this->getParameter('merchant');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setMerchant($value)
	{
		return $this->setParameter('merchant', $value);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setPrivatekey($value)
	{
		return $this->setParameter('privatekey', $value);
	}

	/**
	 * @return mixed
	 */
	public function getPrivatekey()
	{
		return $this->getParameter('privatekey');
	}

	/**
	 * @return mixed
	 */
	public function getAgreement()
	{
		return $this->getParameter('agreement');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setAgreement($value)
	{
		return $this->setParameter('agreement', $value);
	}

	/**
	 * @return mixed
	 */
	public function getPaymentWindowAgreement()
	{
		return $this->getParameter('payment_window_agreement');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setPaymentWindowAgreement($value)
	{
		return $this->setParameter('payment_window_agreement', $value);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setApikey($value)
	{
		return $this->setParameter('apikey', $value);
	}

	/**
	 * @return mixed
	 */
	public function getApikey()
	{
		return $this->getParameter('apikey');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setPaymentWindowApikey($value)
	{
		return $this->setParameter('payment_window_apikey', $value);
	}

	/**
	 * @return mixed
	 */
	public function getPaymentWindowApikey()
	{
		return $this->getParameter('payment_window_apikey');
	}

	/**
	 * @return mixed
	 */
	public function getLanguage()
	{
		return $this->getParameter('language');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setLanguage($value)
	{
		return $this->setParameter('language', $value);
	}

	/**
	 * @return mixed
	 */
	public function getPaymentMethods(){
		return $this->getParameter('payment_methods');
	}

	/**
	 * @param array $value
	 * @return mixed
	 */
	public function setPaymentMethods($value = array()){
		return $this->setParameter('payment_methods', $value);
	}
	
	/**
	 * @return mixed
	 */
	public function getGoogleAnalyticsTrackingID()
	{
		return $this->getParameter('google_analytics_tracking_id');
	}

	/**
	 * @param $value
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setGoogleAnalyticsTrackingID($value)
	{
		return $this->setParameter('google_analytics_tracking_id', $value);
	}

	public function getGoogleAnalyticsClientID()
	{
		return $this->getParameter('google_analytics_client_id');
	}

	public function setGoogleAnalyticsClientID($value)
	{
		return $this->setParameter('google_analytics_client_id', $value);
	}
}
