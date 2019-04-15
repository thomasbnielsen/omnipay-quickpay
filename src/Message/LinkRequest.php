<?php
namespace Omnipay\Quickpay\Message;

use GuzzleHttp\Exception\ClientException;
use Omnipay\Common\Exception\InvalidRequestException;

class LinkRequest extends AbstractRequest
{
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
	 */
	public function getQuickpayParams(): array
	{
		$params = array(
			'version'						=> 'v10',
			'agreement_id'					=> $this->getAgreement(),
			'order_id'						=> $this->getTransactionId(),
			'amount'						=> $this->getAmountInteger(),
			'currency'						=> $this->getCurrency(),
			'continue_url'					=> $this->getReturnUrl(),
			'cancel_url'					=> $this->getCancelUrl(),
			'callback_url'					=> $this->getNotifyUrl(),
			'language'						=> $this->getLanguage(),
			'google_analytics_tracking_id'	=> $this->getGoogleAnalyticsTrackingID(),
			'google_analytics_client_id'	=> $this->getGoogleAnalyticsClientID(),
			'auto_capture'					=> false,
			'payment_methods'				=> $this->getPaymentMethods()
		);

		return $params;
	}

	/**
	 * @return array
	 * @throws InvalidRequestException
	 */
	public function getData(): array
	{
		$this->validate('apikey', 'agreement', 'amount', 'transactionId', 'currency');

		return $this->getQuickpayParams();
	}


	/**
	 * @return Response
	 * @throws InvalidRequestException
	 */
	public function send(): Response
	{
		$fullData	= $this->getData();
		$reference	= $this->getTransactionReference();

		$arrHeaders = [
			'Authorization'			=> ' Basic '.base64_encode(':' . $this->getApikey()),
			'Accept-Version'		=> ' v10',
			'QuickPay-Callback-Url'	=> $this->getNotifyUrl()
		];

		if(!$reference)
		{
			$url	= $this->getEndPoint() . '/' . $this->getTypeOfRequest() . '/';
			$data	= json_encode([
				'order_id'	=> $fullData['order_id'],
				'currency'	=> $fullData['currency'],
			]);

			try
			{
				$httpResponse	= $this->httpClient->request('POST', $url, $arrHeaders, $data);
			}
			catch(ClientException $e)
			{
				$httpResponse	= $e->getResponse();
			}

			$body		= $httpResponse->getBody();
			$response	= json_decode($body, true);
			if(array_key_exists('id', $response))
			{
				$reference	= $response['id'];
			}
			else
			{
				return new LinkResponse($this, $body);
			}
		}
		unset($fullData['order_id'], $fullData['currency']);

		$url		= $this->getEndPoint() . '/' . $this->getTypeOfRequest() . '/' . $reference . '/link';
		$fullData	= json_encode($fullData);

		try {
			$httpResponse	= $this->httpClient->request('PUT', $url, $arrHeaders, $fullData);
		} catch (ClientException $e) {
			$httpResponse	= $e->getResponse();
		}

		return new LinkResponse($this, $httpResponse->getBody(), $reference);
	}


}
