<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * quickpay Capture Response
 */
class CaptureResponse extends AbstractResponse implements RedirectResponseInterface
{
	protected $endpoint = 'https://api.quickpay.net';
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

	public function isRedirect()
	{
		return true;
	}

	public function getRedirectUrl()
	{
		return $this->endpoint.'/payments/7369502/capture?'.http_build_query($this->data);
	}

	public function getRedirectMethod()
	{
		return 'POST';
	}

	public function getRedirectData()
	{
		return $this->data;
	}


	public function getEndpoint()
	{
		// old
		//return "https://secure.quickpay.dk/form/";
		return $this->endpoint;
	}

}
