<?php

namespace Omnipay\Quickpay\Message;

/**
 * Authorize Request
 */
class AuthorizeRequest extends PurchaseRequest
{
	public function getData()
	{
		$data = parent::getData();

		$data['autocapture'] = 0;

		return $this->form_fields($data);
	}

}
