<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Incoming Quickpay callback (acceptNotification). Used by silverstripe-omnipay for
 * capture, refund and void callbacks.
 */
class Notification implements NotificationInterface
{
    use QuickpayResourceTrait;

    /**
     * @var Request
     */
    protected $httpRequest;

    protected $privateKey;

    /**
     * @var object|null|false false = not read yet
     */
    protected $data = false;

    public function __construct(Request $request, $privateKey = null)
    {
        $this->httpRequest = $request;
        $this->privateKey = $privateKey;
    }

    public function setPrivateKey($value)
    {
        $this->privateKey = $value;
        $this->data = false;
        return $this;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Decoded callback body, or null when the request is not a callback.
     *
     * @throws \Omnipay\Common\Exception\InvalidResponseException on a bad checksum or missing key
     * @return object|null
     */
    public function getData()
    {
        if ($this->data === false) {
            $this->data = null;
            if (CallbackVerifier::isCallback($this->httpRequest)) {
                $decoded = json_decode(CallbackVerifier::verify($this->httpRequest, $this->getPrivateKey()));
                $this->data = is_object($decoded) ? $decoded : null;
            }
        }
        return $this->data;
    }

    /**
     * @return object|null
     */
    public function getResource()
    {
        return $this->getData();
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        $data = $this->getData();
        return isset($data->id) ? (string) $data->id : null;
    }

    /**
     * @return string|null Quickpay order_id
     */
    public function getTransactionId()
    {
        return $this->getOrderId();
    }

    /**
     * @return string one of STATUS_COMPLETED, STATUS_PENDING, STATUS_FAILED
     */
    public function getTransactionStatus()
    {
        if (!$this->getData()) {
            return NotificationInterface::STATUS_FAILED;
        }
        $operation = $this->getLatestOperation();
        if (!$operation || !empty($operation->pending)) {
            return NotificationInterface::STATUS_PENDING;
        }
        if (isset($operation->qp_status_code) && (string) $operation->qp_status_code === '20000') {
            return NotificationInterface::STATUS_COMPLETED;
        }
        return NotificationInterface::STATUS_FAILED;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $operation = $this->getData() ? $this->getLatestOperation() : null;
        return ($operation && isset($operation->qp_status_msg)) ? (string) $operation->qp_status_msg : '';
    }
}
