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
		if ($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
			if ($response_body && !empty($response_body->operations)) {
				$data = end($response_body->operations);
				if (!empty($response_body->accepted) && isset($data->qp_status_code) && $data->qp_status_code=="20000") {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Quickpay will return a json object as the body
	 * @return bool|mixed
	 */
	public function getResponseBody(){
		if (is_string($this->data)){
			// JSON is valid
			return $this->data;
		}
		return false;
	}

	/**
	 * @return null|string
	 */
	public function getTransactionReference()
	{
		$response_body = null;
		if ($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
		}
		return isset($response_body->id) ? (string) $response_body->id : '';
	}

	/**
	 * @return null|string
	 */
	public function getCode(){
		if ($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
			if ($response_body && !empty($response_body->operations)) {
				$data = end($response_body->operations);
				if (isset($data->qp_status_code)) {
					return (string) $data->qp_status_code;
				}
			}
			// Error-shaped Quickpay response (auth/validation failure) has no
			// "operations" array at all — just a top-level error code/message.
			return isset($response_body->error) ? (string) $response_body->error : '';
		}
		return null;
	}

	/**
	 * @return null|string
	 */
	public function getMessage(){
		if ($this->getResponseBody()){
			$response_body = json_decode($this->getResponseBody());
			if ($response_body && !empty($response_body->operations)) {
				$data = end($response_body->operations);
				if (isset($data->qp_status_msg)) {
					return $data->type . ': ' . $data->qp_status_msg;
				}
			}
			// Error-shaped Quickpay response (auth/validation failure) has no
			// "operations" array — the real reason is in "message"/"errors".
			if ($response_body && isset($response_body->message)) {
				$errors = isset($response_body->errors) ? ' ' . json_encode($response_body->errors) : '';
				return $response_body->message . $errors;
			}
			return '';
		}
		return null;
	}
} 
