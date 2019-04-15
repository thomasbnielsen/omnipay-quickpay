<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
	/**
	 * @return bool
	 */
	public function isSuccessful(): bool
	{
		if($this->getResponseBody())
		{
			$response_body = json_decode($this->getResponseBody());
			$data	= end($response_body->operations);
			if($response_body->accepted && $data->qp_status_code === '20000')
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Quickpay will return a json object as the body
	 * @return bool|mixed
	 */
	public function getResponseBody()
	{
		if(is_string($this->data))
		{
			// JSON is valid
			return $this->data;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getTransactionReference(): string
	{
		if($this->getResponseBody())
		{
			$response_body	= json_decode($this->getResponseBody());
		}

		return $response_body->id ?? '';
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		if($this->getResponseBody())
		{
			$response_body	= json_decode($this->getResponseBody());
			$data			= end($response_body->operations);
			return $data->qp_status_code ?? '';
		}

		return null;
	}

	/**
	 * @return null|string
	 */
	public function getMessage(): ?string
	{
		if($this->getResponseBody())
		{
			$response_body	= json_decode($this->getResponseBody());
			$data			= end($response_body->operations);
			return isset($data->qp_status_msg) ? $data->type . ': ' . $data->qp_status_msg : '';
		}

		return null;
	}
}
