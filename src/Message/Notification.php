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
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $httpRequest;

    protected $privateKey;

    protected $data;

    public function setPrivateKey($value)
    {
        $this->privateKey = $value;
        return $this;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function __construct(Request $request, $privateKey = null)
    {
        $this->httpRequest = $request;
        $this->privateKey = $privateKey;
    }

    public function getData()
    {
        if ($this->data){
            return $this->data;
        }

        if ($this->httpRequest->headers->get('Content-Type') == "application/json") {
            $data = json_decode($this->httpRequest->getContent());

            $headerChecksum = $this->httpRequest->headers->get('Quickpay-Checksum-Sha256');
            // validate with accounts private key.
            $checksum = hash_hmac("sha256", $this->httpRequest->getContent(), $this->getPrivateKey());
            if ($checksum != $headerChecksum) {
                throw new InvalidResponseException("Checksum mismatch checksum:$checksum != header:$headerChecksum");
            }

            $this->data = $data;
        }

        return $this->data;
    }

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        if ($data = $this->getData()) {
            return $data->id;
        }
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        if ($data = $this->getData()){
            if ($op = $this->getLatestOperation()) {
				if ($op->pending == false && $op->qp_status_code == "20000") {
					return NotificationInterface::STATUS_COMPLETED;
				} else if ($op->pending) {
					return NotificationInterface::STATUS_PENDING;
				}
			}
        }

        return NotificationInterface::STATUS_FAILED;
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        if ($data = $this->getData()){
            if ($op = $this->getLatestOperation()) {
				return $op->qp_status_msg;
			}
        }
        return '';
    }

	/**
	 * Get latest operation
	 *
	 * @return bool|mixed
	 */
    public function getLatestOperation() {
		if ($data = $this->getData()) {
			return end($data->operations);
		}
		return false;
	}
}
