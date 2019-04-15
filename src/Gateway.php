<?php

namespace Omnipay\Quickpay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\BadMethodCallException;
use Omnipay\Quickpay\Message\AuthorizeRequest;
use Omnipay\Quickpay\Message\CaptureRequest;
use Omnipay\Quickpay\Message\CompleteRequest;
use Omnipay\Quickpay\Message\LinkRequest;
use Omnipay\Quickpay\Message\Notification;
use Omnipay\Quickpay\Message\NotifyRequest;
use Omnipay\Quickpay\Message\PurchaseRequest;
use Omnipay\Quickpay\Message\RecurringRequest;
use Omnipay\Quickpay\Message\RefundRequest;
use Omnipay\Quickpay\Message\VoidRequest;

/**
 * Quickpay Gateway
 */
class Gateway extends AbstractGateway
{
	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'Quickpay';
	}

	/**
	 * @return array
	 */
	public function getDefaultParameters(): array
	{
		parent::getDefaultParameters();

		return [
			'type'							=> '',
			'merchant'						=> '',
			'agreement'						=> '',
			'apikey'						=> '',
			'privatekey'					=> '',
			'language'						=> '',
			'google_analytics_tracking_id'	=> '',
			'google_analytics_client_id'	=> '',
			'description'					=> '',
			'order_id'						=> '',
			'synchronized'					=> false,
			'payment_methods'				=> array()
		];
	}

	/**
	 * @return array
	 */
	public function getPaymentMethods(): array
	{
		return $this->getParameter('payment_methods');
	}

	/**
	 * @param array $value
	 * @return mixed
	 */
	public function setPaymentMethods(array $value = [])
	{
		return $this->setParameter('payment_methods', $value);
	}

	/**
	 * @return int
	 */
	public function getMerchant(): int
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
	public function getAgreement(): int
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
	 * @param string $value
	 * @return mixed
	 */
	public function setApikey(string $value)
	{
		return $this->setParameter('apikey', $value);
	}

	/**
	 * @return string
	 */
	public function getApikey(): string
	{
		return $this->getParameter('apikey');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setPrivatekey(string $value)
	{
		return $this->setParameter('privatekey', $value);
	}

	/**
	 * @return string
	 */
	public function getPrivatekey(): string
	{
		return $this->getParameter('privatekey');
	}

	/**
	 * @return string
	 */
	public function getLanguage(): string
	{
		return $this->getParameter('language');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setLanguage(string $value)
	{
		return $this->setParameter('language', $value);
	}

	/**
	 * @return string
	 */
	public function getGoogleAnalyticsTrackingID(): string
	{
		return $this->getParameter('google_analytics_tracking_id');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setGoogleAnalyticsTrackingID(string $value)
	{
		return $this->setParameter('google_analytics_tracking_id', $value);
	}

	/**
	 * @return string
	 */
	public function getGoogleAnalyticsClientID(): string
	{
		return $this->getParameter('google_analytics_client_id');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setGoogleAnalyticsClientID(string $value)
	{
		return $this->setParameter('google_analytics_client_id', $value);
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->getParameter('type');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setType(string $value)
	{
		return $this->setParameter('type', $value);
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->getParameter('description');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setDescription(string $value)
	{
		return $this->setParameter('description', $value);
	}

	/**
	 * @return string
	 */
	public function getOrderID(): string
	{
		return $this->getParameter('order_id');
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	public function setOrderID(string $value)
	{
		return $this->setParameter('order_id', $value);
	}

	/**
	 * @param bool $value
	 * @return Gateway
	 */
	public function setSynchronized(bool $value): Gateway
	{
		return $this->setParameter('synchronized', $value);
	}

	/**
	 * @return boolean
	 */
	public function getSynchronized(): bool
	{
		return (bool)$this->getParameter('synchronized');
	}

	/**
	 * Start an authorize request
	 *
	 * @param array $parameters array of options
	 * @return AuthorizeRequest
	 */
	public function authorize(array $parameters = []): AuthorizeRequest
	{
		return $this->createRequest(AuthorizeRequest::class, $parameters);
	}

	/**
	 * Start a purchase request
	 *
	 * @param array $parameters array of options
	 * @return PurchaseRequest
	 */
	public function purchase(array $parameters = []): PurchaseRequest
	{
		return $this->createRequest(PurchaseRequest::class, $parameters);
	}

	/**
	 * @param array $parameters
	 * @return CaptureRequest
	 */
	public function capture(array $parameters = []): CaptureRequest
	{
		return $this->createRequest(CaptureRequest::class, $parameters);
	}

	/**
	 * @param array $parameters
	 * @return VoidRequest
	 */
	public function void(array $parameters = []): VoidRequest
	{
		return $this->createRequest(VoidRequest::class, $parameters);
	}

	/**
	 * @param array $parameters
	 * @return RefundRequest
	 */
	public function refund(array $parameters = []): RefundRequest
	{
		return $this->createRequest(RefundRequest::class, $parameters);
	}

	/**
	 * @param array $parameters
	 * @return RecurringRequest
	 */
	public function recurring(array $parameters = []): RecurringRequest
	{
		return $this->createRequest(RecurringRequest::class, $parameters);
	}

	/**
	 * Is used for callbacks coming in to the system
	 * notify will verify these callbacks and eventually return the body of the callback to the app
	 * @param array $parameters
	 * @return NotifyRequest
	 */
	public function notify(array $parameters = []): NotifyRequest
	{
		return $this->createRequest(NotifyRequest::class, $parameters);
	}

	/**
	 * Complete a purchase
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completePurchase(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete an authorization
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeAuthorize(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * A complete request
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeRequest(array $parameters = []): CompleteRequest
	{
		return $this->createRequest(CompleteRequest::class, $parameters);
	}

	/**
	 * Complete capture
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeCapture(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete cancel
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeVoid(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete refund
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeRefund(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * Complete recurring
	 *
	 * @param array $parameters
	 * @return CompleteRequest
	 */
	public function completeRecurring(array $parameters = []): CompleteRequest
	{
		return $this->completeRequest($parameters);
	}

	/**
	 * @return Notification
	 */
	public function acceptNotification(): Notification
	{
		return new Notification($this->httpRequest, $this->getPrivatekey());
	}

	/**
	 * @return bool
	 */
	public function supportsCreateCard(): bool
	{
		return false;
	}

	public function createCard(array $options = [])
	{
		throw new BadMethodCallException('Method createCard() not supported');
	}

	/**
	 * @return bool
	 */
	public function supportsUpdateCard(): bool
	{
		return false;
	}

	public function updateCard(array $options = [])
	{
		throw new BadMethodCallException('Method updateCard() not supported');
	}

	/**
	 * @return bool
	 */
	public function supportsDeleteCard(): bool
	{
		return false;
	}

	public function deleteCard(array $options = [])
	{
		throw new BadMethodCallException('Method deleteCard() not supported');
	}

	/**
	 * @return bool
	 */
	public function supportsFetchTransaction(): bool
	{
		return false;
	}

	public function fetchTransaction(array $options = [])
	{
		throw new BadMethodCallException('Method fetchTransaction() not supported');
	}

	/**
	 * @param array $parameters
	 * @return LinkRequest
	 */
	public function link(array $parameters = []): LinkRequest
	{
		return $this->createRequest(LinkRequest::class, $parameters);
	}
}
