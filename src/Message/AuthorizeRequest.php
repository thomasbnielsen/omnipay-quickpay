<?php

namespace Omnipay\Quickpay\Message;

/**
 * Authorize Request
 */
class AuthorizeRequest extends PurchaseRequest
{
	public function getData()
	{
		$data = $this->getQuickpayParams();
		$data['autocapture'] = 0;
		return $this->createChecksum($data);
	}

}
