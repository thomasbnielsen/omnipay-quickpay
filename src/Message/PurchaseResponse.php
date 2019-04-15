<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Quickpay Purchase Response
 */
class PurchaseResponse extends Response implements RedirectResponseInterface
{
	/**
	 * @var string
	 */
	protected $endpoint = 'https://payment.quickpay.net';

	/**
	 * @return bool
	 */
	public function isSuccessful(): bool
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isRedirect(): bool
	{
		return true;
	}

	/**
	 * @return string
	 */
	public function getRedirectUrl(): string
	{
		return $this->endpoint;
	}

	/**
	 * @return string
	 */
	public function getRedirectMethod(): string
	{
		return 'POST';
	}

	/**
	 * @return mixed
	 */
	public function getRedirectData()
	{
		return $this->data;
	}
}
