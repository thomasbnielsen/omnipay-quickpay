<?php


namespace Omnipay\Quickpay\Message;

/**
 * quickpay Cancel Request
 */
class VoidRequest extends AbstractRequest
{
	public function __construct($httpClient, $httpRequest)
	{
		parent::__construct($httpClient, $httpRequest);
		$this->setApiMethod('cancel');
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return [
			'id'	=> $this->getTransactionReference()
		];
	}
}
