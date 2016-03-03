<?php


namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class RefundRequest extends AbstractRequest
{
	public function __construct($httpClient, $httpRequest)
	{
		parent::__construct($httpClient, $httpRequest);
		$this->setApiMethod('refund');
	}
}
