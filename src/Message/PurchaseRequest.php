<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;


/**
 * quickpay Purchase Request
 * Docs can be found at http://tech.quickpay.net/payments/form/
 */
class PurchaseRequest extends AbstractRequest
{
	/** @var string  */
	protected $endpoint = 'https://payment.quickpay.net';

	/**
	 * @return array
	 */
	public function getQuickpayParams(): array
	{
		$params = array(
			'version'						=> 'v10',
			'merchant_id'					=> $this->getMerchant(),
			'agreement_id'					=> $this->getAgreement(),
			'order_id'						=> $this->getTransactionId(),
			'amount'						=> $this->getAmountInteger(),
			'currency'						=> $this->getCurrency(),
			'continue_url'					=> $this->getReturnUrl(),
			'cancel_url'					=> $this->getCancelUrl(),
			'callback_url'					=> $this->getNotifyUrl(),
			'language'						=> $this->getLanguage(),
			'google_analytics_tracking_id'	=> $this->getGoogleAnalyticsTrackingID(),
			'autocapture'					=> 1,
			'type'							=> $this->getType(),
			'payment_methods'				=> $this->getPaymentMethods()
		);

		// it seems description param is not always allowed, depending on the Type set
		if($this->getDescription() !== '')
		{
			$params['description']	= $this->getDescription();
		}

		return $params;
	}

	public function setTransactionId($value)
	{
		$value = str_pad($value, 4, '0', STR_PAD_LEFT);
		if(strlen($value) > 24) {
			throw new InvalidRequestException('transactionId has a max length of 24');
		}
		return parent::setTransactionId($value);
	}

	/**
	 * @return array
	 * @throws InvalidRequestException
	 */
	public function getData(): array
	{
		// checks if any of these are empty, so we can throw an error without calling the API
		$this->validate('merchant', 'agreement', 'amount', 'transactionId');

		return $this->createChecksum($this->getQuickpayParams());
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function createChecksum(array $data): array
	{
		$data['checksum'] = $this->sign($data, $this->getApikey());
		return $data;
	}

	// taken from quickpays PHP example on how to calculate checksum for the payment form
	/**
	 * @param array $params
	 * @param $api_key
	 * @return string
	 */
	public function sign(array $params, $api_key): string
	{
		$flattened_params = $this->flatten_params($params);
		ksort($flattened_params);
		$base = implode(' ', $flattened_params);

		return hash_hmac('sha256', $base, $api_key);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function flatten_params(array $params): array
	{
		return iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($params)), true);
	}

	/**
	 * @param mixed $data
	 * @return PurchaseResponse
	 */
	public function sendData($data): Response
	{
		return $this->response = new PurchaseResponse($this, $data);
	}
}
