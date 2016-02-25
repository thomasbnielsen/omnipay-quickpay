<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
	public function getMerchant()
	{
		return $this->getParameter('merchant');
	}

	public function setMerchant($value)
	{
		return $this->setParameter('merchant', $value);
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
