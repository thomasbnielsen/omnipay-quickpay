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

	public function getData()
	{
		$data = array(
			'id' => $this->getTransactionReference()
		);
		return $data;
	}
}
