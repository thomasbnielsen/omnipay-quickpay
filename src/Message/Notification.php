<?php

namespace Omnipay\Quickpay\Message;


use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\Request;

class Notification implements NotificationInterface
{

	/**
	 * The HTTP request object.
	 *
	 * @var Request
	 */
	protected $httpRequest;

	protected $privateKey;

	protected $data;

	/**
	 * @param string $value
	 * @return Notification
	 */
	public function setPrivateKey(string $value): Notification
	{
		$this->privateKey = $value;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getPrivateKey(): ?string
	{
		return $this->privateKey;
	}

	/**
	 * Notification constructor.
	 * @param Request $request
	 * @param string|null $privateKey
	 */
	public function __construct(Request $request, string $privateKey = null)
	{
		$this->httpRequest = $request;
		$this->privateKey = $privateKey;
	}

	public function getData()
	{
		if($this->data)
		{
			return $this->data;
		}

		if($this->httpRequest->headers->get('Content-Type') === 'application/json')
		{
			$data				= json_decode($this->httpRequest->getContent());
			$header_checksum	= $this->httpRequest->headers->get('Quickpay-Checksum-Sha256');
			// validate with accounts private key.
			$our_checksum		= hash_hmac('sha256', $this->httpRequest->getContent(), $this->getPrivateKey());

			if($our_checksum !== $header_checksum)
			{
				throw new InvalidResponseException;
			}

			$this->data = $data;
		}

		return json_encode($this->data);
	}

	/**
	 * Gateway Reference
	 * A reference provided by the gateway to represent this transaction
	 *
	 * @return string|null
	 * @throws InvalidResponseException
	 */
	public function getTransactionReference(): ?string
	{
		if($data = $this->getData())
		{
			return $data->id;
		}

		return null;
	}

	/**
	 * Was the transaction successful?
	 *
	 * Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
	 * or {@see #STATUS_FAILED}.
	 *
	 * @return string
	 * @throws InvalidResponseException
	 */
	public function getTransactionStatus(): string
	{
		if($data = $this->getData())
		{
			$op	= end($data->operations);
			if($op->pending === false && $op->qp_status_code === '20000')
			{
				return NotificationInterface::STATUS_COMPLETED;
			}

			if($op->pending)
			{
				return NotificationInterface::STATUS_PENDING;
			}
		}

		return NotificationInterface::STATUS_FAILED;
	}

	/**
	 * Response Message
	 * A response message from the payment gateway
	 *
	 * @return string
	 * @throws InvalidResponseException
	 */
	public function getMessage(): string
	{
		if($data = $this->getData())
		{
			return end($data->operations)->qp_status_msg;
		}
		return '';
	}
}
