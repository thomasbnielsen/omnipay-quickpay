<?php

namespace Omnipay\Quickpay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Quickpay\Message\CaptureRequest;
use Omnipay\Quickpay\Message\AuthorizeRequest;
use Omnipay\Quickpay\Message\CompletePurchaseRequest;
use Omnipay\Quickpay\Message\PurchaseRequest;

/**
 * Quickpay Gateway
 */
class Gateway extends AbstractGateway
{
	public function getName()
	{
		return 'Quickpay';
	}

  
	public function getDefaultParameters()
	{
		return array(
			'merchant' => '',
			'agreement' => '',
			'apikey' => '',
			'language' => '',
			//'callbackurl' => '',
			'continueurl' => '',
			'cancelurl' => '',
			//'payment_methods' => array("creditcard, !jcb, !visa-us, !maestro")
		);
	}

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

	public function setApikey($value)
	{
		return $this->setParameter('apikey', $value);
	}

	public function getApikey()
	{
		return $this->getParameter('apikey');
	}

	public function getLanguage()
	{
		return $this->getParameter('language');
	}

	public function setLanguage($value)
	{
		return $this->setParameter('language', $value);
	}

	public function authorize(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\AuthorizeRequest', $parameters);
	}

	// this will auto capture for some reason
	public function purchase(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\PurchaseRequest', $parameters);
	}

	/**
	 * @param array $parameters
	 * @return CompletePurchaseRequest
	 */
	public function completePurchase(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CompletePurchaseRequest', $parameters);
	}

	/**
	 * @param array $parameters
	 * @return CaptureRequest
	 */
	public function capture(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CaptureRequest', $parameters);
	}


}
