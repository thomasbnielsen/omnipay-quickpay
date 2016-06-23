<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;
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
    public function isSuccessful()
    {
        return false;
    }

	/**
	 * @return bool
	 */
    public function isRedirect()
    {
        return true;
    }

	/**
	 * @return string
	 */
    public function getRedirectUrl()
    {
        return $this->endpoint;
    }

	/**
	 * @return string
	 */
    public function getRedirectMethod()
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
