<?php

namespace Omnipay\Quickpay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Quickpay\Message\Notification;

/**
 * Quickpay Gateway
 */
class Gateway extends AbstractGateway
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'Quickpay';
	}

	/**
	 * @return array
	 */
	public function getDefaultParameters()
	{
		parent::getDefaultParameters();
		return array(
			'type' => '',
			'merchant' => '',
			'agreement' => '',
			'apikey' => '',
			'privatekey' => '',
			'language' => '',
			'google_analytics_tracking_id' => '',
			'payment_methods' => array()
		);
	}

	/**
	 * @return array
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
	 * @return int
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
	 * @return int
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
	 * @param $value
	 * @return mixed
	 */
	public function setApikey($value)
	{
		return $this->setParameter('apikey', $value);
	}

	/**
	 * @return string
	 */
	public function getApikey()
	{
		return $this->getParameter('apikey');
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
	 * @return string
	 */
	public function getPrivatekey()
	{
		return $this->getParameter('privatekey');
	}

	/**
	 * @return string
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
	 * @return string
	 */
	public function getGoogleAnalyticsTrackingID()
	{
		return $this->getParameter('google_analytics_tracking_id');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setGoogleAnalyticsTrackingID($value)
	{
		return $this->setParameter('google_analytics_tracking_id', $value);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->getParameter('type');
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function setType($value)
	{
		return $this->setParameter('google_analytics_tracking_id', $value);
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

	/**
	 * @return Notification
	 */
	public function acceptNotification()
	{
		return new Notification($this->httpRequest, $this->getPrivatekey());
	}
}
