<?php


namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CancelRequest extends AbstractRequest
{
	public function __construct($httpClient, $httpRequest)
	{
		parent::__construct($httpClient, $httpRequest);
		$this->setApiMethod('cancel');
	}
}
