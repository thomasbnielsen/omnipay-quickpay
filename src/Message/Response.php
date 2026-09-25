<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response for a Quickpay payment/subscription resource (API reply or verified callback body).
 *
 * getData()/getResponseBody() return the raw JSON string as received.
 *
 * - isSuccessful(): HTTP 2xx (or a verified callback), resource accepted, and the
 *   expected (or latest) operation finished with qp_status_code 20000.
 * - isPending(): the resource exists but the operation is still processing
 *   (e.g. an asynchronous 202 answer).
 * - Anything else is a failure; getMessage()/getCode() explain why, including
 *   Quickpay error bodies and non-JSON replies.
 */
class Response extends AbstractResponse
{
    use QuickpayResourceTrait;

    /**
     * Operation type this response is judged on; null = latest operation.
     *
     * @var string|null
     */
    protected $expectedOperation = null;

    /**
     * @var int|null
     */
    protected $httpStatus;

    /**
     * @var string|null
     */
    protected $reference;

    /**
     * @var object|null|false false = not decoded yet
     */
    private $resource = false;

    public function __construct(RequestInterface $request, $data, ?int $httpStatus = null, ?string $reference = null)
    {
        parent::__construct($request, $data);
        $this->httpStatus = $httpStatus;
        $this->reference = $reference;
    }

    /**
     * Raw body when the data is a string, otherwise false.
     *
     * @return string|false
     */
    public function getResponseBody()
    {
        return is_string($this->data) ? $this->data : false;
    }

    /**
     * Decoded body, only when it is a JSON object.
     *
     * @return object|null
     */
    public function getResource()
    {
        if ($this->resource === false) {
            $this->resource = null;
            if (is_string($this->data) && $this->data !== '') {
                $decoded = json_decode($this->data);
                if (is_object($decoded)) {
                    $this->resource = $decoded;
                }
            } elseif (is_object($this->data)) {
                $this->resource = $this->data;
            }
        }
        return $this->resource;
    }

    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    protected function isHttpOk(): bool
    {
        return $this->httpStatus === null || ($this->httpStatus >= 200 && $this->httpStatus < 300);
    }

    protected function hasResourceId(): bool
    {
        $resource = $this->getResource();
        return $resource && !empty($resource->id);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        if (!$this->isHttpOk() || !$this->hasResourceId() || !$this->isAccepted()) {
            return false;
        }
        $operation = $this->getLatestOperation($this->expectedOperation);
        return $operation
            && empty($operation->pending)
            && isset($operation->qp_status_code)
            && (string) $operation->qp_status_code === '20000';
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        if (!$this->isHttpOk() || !$this->hasResourceId()) {
            return false;
        }
        $operation = $this->getLatestOperation($this->expectedOperation);
        if (!$operation) {
            return $this->httpStatus === 202;
        }
        return !empty($operation->pending);
    }

    /**
     * Payment or subscription id as a string.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if ($this->reference !== null && $this->reference !== '') {
            return $this->reference;
        }
        $resource = $this->getResource();
        return isset($resource->id) ? (string) $resource->id : '';
    }

    /**
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getOrderId();
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        $operation = $this->getLatestOperation($this->expectedOperation);
        if ($operation && isset($operation->qp_status_code)) {
            return (string) $operation->qp_status_code;
        }
        $resource = $this->getResource();
        if ($resource && isset($resource->error_code)) {
            return (string) $resource->error_code;
        }
        if ($resource && isset($resource->error)) {
            return (string) $resource->error;
        }
        return $this->httpStatus !== null ? (string) $this->httpStatus : null;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        $operation = $this->getLatestOperation($this->expectedOperation);
        if ($operation && isset($operation->qp_status_msg)) {
            return trim(($operation->type ?? '') . ': ' . $operation->qp_status_msg, ': ');
        }
        if ($operation && !empty($operation->pending)) {
            return ($operation->type ?? 'operation') . ': pending';
        }

        $resource = $this->getResource();
        if ($resource && isset($resource->message)) {
            $message = (string) $resource->message;
            if (!empty($resource->errors)) {
                $message .= ' ' . json_encode($resource->errors);
            }
            return $message;
        }

        $body = $this->getResponseBody();
        if ($this->httpStatus !== null && !$this->isHttpOk()) {
            $snippet = $body ? mb_substr(trim(strip_tags($body)), 0, 200) : '';
            return trim('HTTP ' . $this->httpStatus . ': ' . $snippet);
        }

        return $body === false ? null : '';
    }
}
