<?php

namespace Omnipay\Quickpay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Quickpay\Message\Notification;

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
			'privatekey' => '',
			'language' => '',
			'payment_methods' => array()
		);
	}

	public function getPaymentMethods(){
		return $this->getParameter('payment_methods');
	}

	public function setPaymentMethods($value = array()){
		return $this->setParameter('payment_methods', $value);
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

	public function setPrivatekey($value)
	{
		return $this->setParameter('privatekey', $value);
	}

	public function getPrivatekey()
	{
		return $this->getParameter('privatekey');
	}

	public function getLanguage()
	{
		return $this->getParameter('language');
	}

	public function setLanguage($value)
	{
		return $this->setParameter('language', $value);
	}

	/**
	 * Start an authorize request
	 *
	 * @param array $parameters array of options
	 * @return \Omnipay\Quickpay\Message\AuthorizeRequest
	 */
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
	 * @return \Omnipay\Quickpay\Message\CompletePurchaseRequest
	 */
	public function completeAuthorize(array $parameters = array())
	{
		return $this->completePurchase($parameters);
	}

	/**
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CaptureRequest
	 */
	public function capture(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CaptureRequest', $parameters);
	}

	/**
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CancelRequest
	 */
	public function cancel(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CancelRequest', $parameters);
	}

	/**
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\RefundRequest
	 */
	public function refund(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\RefundRequest', $parameters);
	}

	/**
	 * Is used for callbacks coming in to the system
	 * notify will verify these callbacks and eventually return the body of the callback to the app
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\NotifyRequest
	 */
	public function notify(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\NotifyRequest', $parameters);
	}

	/**
	 * A complete request
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompleteRequest
	 */
	public function completeRequest(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Quickpay\Message\CompleteRequest', $parameters);
	}

	/**
	 * Complete capture
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompleteRequest
	 */
	public function completeCapture(array $parameters = array())
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete cancel
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompleteRequest
	 */
	public function completeCancel(array $parameters = array())
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete refund
	 *
	 * @param array $parameters
	 * @return \Omnipay\Quickpay\Message\CompleteRequest
	 */
	public function completeRefund(array $parameters = array())
	{
		return $this->completeRequest($parameters);
	}

	public function acceptNotification()
	{
		return new Notification($this->httpRequest, $this->getPrivatekey());
	}
}
