<?php

namespace Omnipay\Quickpay\Message;

/**
 * Authorize Request
 */
class AuthorizeRequest extends PurchaseRequest
{
	/**
	 * @return array
	 */
	public function getData(): array
	{
		$data					= $this->getQuickpayParams();
		$data['autocapture']	= 0;
		return $this->createChecksum($data);
	}
}
