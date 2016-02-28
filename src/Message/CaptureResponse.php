<?php

namespace Omnipay\Quickpay\Message;

// TODO

use Omnipay\Common\Message\AbstractResponse;

/**
 * quickpay Capture Response
 */
class CaptureResponse extends AbstractResponse
{
	/*
		public function isSuccessful()
		{
			$data = $this->getData();
			return isset($data['captureResult']) && $data['captureResult'];
		}
	*/

	public function isSuccessful()
	{
		return false;
	}

}
