<?php


namespace Omnipay\Quickpay\Message;

/**
 * While Omnipay does not currently support recurring payments, the API to do this in Quickpay is very similar to normal payments
 * We can therefore create a little bit of functionality, to able to use Omnipay for recurring payments
 *
 * quickpay create Recurring payment Request
 */
class RecurringRequest extends AbstractRequest
{
	public function __construct($httpClient, $httpRequest)
	{
		parent::__construct($httpClient, $httpRequest);
		$this->setApiMethod('recurring');
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		$data = parent::getData();
		$data['order_id'] = $this->getOrderID();
		$data['auto_capture'] = 1;
		return $data;
	}
}
