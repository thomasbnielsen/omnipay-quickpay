<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
	/**
	 * @return bool
	 */
	public function isSuccessful()
	{
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
			$data = end($response_body->operations);
			if ($data->qp_status_code=="20000") {
				return true;
			}
		}

		return false;
	}

	/**
	 * Quickpay will return a json object as the body
	 * @return bool|mixed
	 */
	public function getResponseBody(){
		if(is_string($this->data)){
			$response_body = json_decode($this->data);
			if (json_last_error() === JSON_ERROR_NONE) {
				// JSON is valid
				return $response_body;
			}
		}
		return false;
	}

	/**
	 * @return null|string
	 */
	public function getTransactionReference()
	{
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
		}
		return isset($response_body->id) ? $response_body->id : '';
	}

	/**
	 * @return null|string
	 */
	public function getCode(){
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
			$data = end($response_body->operations);
			return isset($data->qp_status_code) ? $data->qp_status_code : '';
		}
		return null;
	}

	/**
	 * @return null|string
	 */
	public function getMessage(){
		if($this->getResponseBody()){
			$response_body = $this->getResponseBody();
			$data = end($response_body->operations);
			return isset($data->qp_status_msg) ? $data->type . ': ' . $data->qp_status_msg : '';
		}
		return null;
	}
} 