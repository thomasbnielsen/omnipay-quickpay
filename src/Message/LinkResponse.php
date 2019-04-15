<?php
namespace Omnipay\Quickpay\Message;


use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

class LinkResponse extends Response implements RedirectResponseInterface
{
	protected $reference;

	public function __construct(RequestInterface $request, $data, $reference = null)
	{
		parent::__construct($request, $data);
		$this->reference	= $reference;
	}

	/**
	 * @return bool
	 */
	public function isSuccessful(): bool
	{
		$body	= json_decode($this->getResponseBody());
		if(isset($body->url))
		{
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getTransactionReference(): string
	{
		return $this->reference;
	}

	/**
	 * @return bool
	 * @codeCoverageIgnore
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
		$data	= json_decode($this->getResponseBody());
		return $data->url;
	}

}
