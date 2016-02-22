<?php

namespace Omnipay\Quickpay\Message;

/**
 * quickpay Capture Request
 */
class CaptureRequest extends AbstractRequest
{
    protected $endpoint = 'https://api.quickpay.net';
// currently not working

    public function getData()
    {
        $this->validate('amount');

		// data to be send
		$data = array();
		$data['version'] = "v10";
		//$data['merchant_id'] = $this->getParameter('merchant');
		//$data['agreement_id'] = $this->getParameter('agreement');
		$data['amount'] = $this->getParameter('amount') * 100;
		//$data['transactionId'] = $this->getParameter('transactionId');

        return $data;
    }

    /**
     * @param mixed $data
     * @return CaptureResponse
     */
	public function sendData($data)
	{
		return $this->response = new CaptureResponse($this, $data);
	}


    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
        return $this->sendData($this->getData());
    }

	public function setTransactionId($value)
	{
		return $this->setParameter('transactionId', $value);
	}
}
