<?php

namespace Omnipay\Quickpay\Message;

use GuzzleHttp\Exception\ClientException;

/**
 * Quickpay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

	/** @var string */
	protected $endpoint = 'https://api.quickpay.net/';

	/** @var string */
	private $apimethod = 'capture';

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		$url	= $this->getEndPoint() . $this->getTypeOfRequest() . '/' . $this->getTransactionReference() . '/' . $this->getApiMethod();
		if($this->getSynchronized())
		{
			$url .= '?synchronized';
			return $url;
		}
		return $url;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return [
			'id'		=> $this->getTransactionReference(),
			'amount'	=> $this->getAmountInteger()
		];
	}

	/**
	 * @return string
	 */
	public function getHttpMethod(): string
	{
		return 'POST';
	}

	/**
	 * @param mixed $data
	 * @return Response
	 */
	public function sendData($data): Response
	{
		$arrHeaders	= [
			'Authorization'			=> ' Basic '.base64_encode(':' . $this->getApikey()),
			'Accept-Version'		=> ' v10',
			'QuickPay-Callback-Url'	=> $this->getNotifyUrl()
		];

		$url	= $this->getUrl();
		if(is_array($data) && array_key_exists('synchronized', $data))
		{
			unset($data['synchronized']);
			$data	= json_encode($data);
		}

		try {
			$httpResponse	= $this->httpClient->request(
				$this->getHttpMethod(),
				$url,
				$arrHeaders,
				$data
			);
		}
		catch(ClientException $e)
		{
			$httpResponse	= $e->getResponse();
		}

		$this->response	= new Response($this, $httpResponse->getBody());
		return $this->response;
	}

	/**
	 * @return Response
	 */
	public function send(): Response
	{
		return $this->sendData($this->getData());
	}

	public function getTypeOfRequest(): string
	{
		$type = 'payments';
		if($this->getType() === 'subscription')
		{
			$type	= 'subscriptions';
		}
		return $type;
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setApiMethod($value): AbstractRequest
	{
		$this->apimethod = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApiMethod(): string
	{
		return $this->apimethod;
	}

	/**
	 * @return string
	 */
	public function getEndPoint(): string
	{
		return $this->endpoint;
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
	 * @return AbstractRequest
	 */
	public function setMerchant($value): AbstractRequest
	{
		return $this->setParameter('merchant', $value);
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setPrivatekey($value): AbstractRequest
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
	 * @return int
	 */
	public function getAgreement(): int
	{
		return $this->getParameter('agreement');
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setAgreement($value): AbstractRequest
	{
		return $this->setParameter('agreement', $value);
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setApikey($value): AbstractRequest
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
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setSynchronized($value): AbstractRequest
	{
		return $this->setParameter('synchronized', $value);
	}

	/**
	 * @return bool
	 */
	public function getSynchronized(): bool
	{
		return (bool)$this->getParameter('synchronized');
	}

	/**
	 * @return string
	 */
	public function getLanguage(): string
	{
		return $this->getParameter('language');
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setLanguage($value): AbstractRequest
	{
		return $this->setParameter('language', $value);
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
	 * @return AbstractRequest
	 */
	public function setPaymentMethods(array $value = []): AbstractRequest
	{
		return $this->setParameter('payment_methods', $value);
	}

	/**
	 * @return string
	 */
	public function getGoogleAnalyticsTrackingID(): string
	{
		return $this->getParameter('google_analytics_tracking_id');
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setGoogleAnalyticsTrackingID($value): AbstractRequest
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
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setGoogleAnalyticsClientID($value): AbstractRequest
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
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setType($value): AbstractRequest
	{
		return $this->setParameter('type', $value);
	}

	/**
	 * @return string
	 */
	public function getOrderID(): string
	{
		return $this->getParameter('order_id');
	}

	/**
	 * @param $value
	 * @return AbstractRequest
	 */
	public function setOrderID($value): AbstractRequest
	{
		return $this->setParameter('order_id', $value);
	}
}
