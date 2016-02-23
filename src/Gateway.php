<?php

namespace Omnipay\Quickpay;

use Omnipay\Common\AbstractGateway;

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
		parent::getDefaultParameters();
		return array(
			'merchant' => '',
			'agreement' => '',
			'apikey' => '',
			'language' => ''
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

	/**
	 * Start a purchase request
	 *
	 * @param array $parameters array of options
	 * @return \Omnipay\Quickpay\Message\PurchaseRequest
	 */
	public function purchase(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\PurchaseRequest', $parameters);
	}

	/**
	 * Complete a purchase
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompletePurchaseRequest
	 */
	public function completePurchase(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CompletePurchaseRequest', $parameters);
	}

	/**
	 * Complete an authorization
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompleteAuthorizeRequest
	 */
	public function completeAuthorize(array $parameters = array())
	{
		return $this->completePurchase($parameters);
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
