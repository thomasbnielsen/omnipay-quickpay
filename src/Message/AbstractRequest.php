<?php

namespace Omnipay\Quickpay\Message;

/**
 * Base class for requests against the Quickpay v10 REST API.
 *
 * Routing: `type` = "subscription" targets /subscriptions, anything else /payments.
 * `synchronized` appends ?synchronized so Quickpay answers with the final result
 * instead of a pending 202.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://api.quickpay.net/';

    /**
     * @var string
     */
    private $apimethod = '';

    /**
     * @var string
     */
    private $httpMethod = 'POST';

    /**
     * Response class used by sendData()
     *
     * @var string
     */
    protected $responseClass = Response::class;

    /**
     * Builds an API URL from path segments, without double slashes.
     */
    protected function buildUrl(array $segments, bool $allowSynchronized = true): string
    {
        $parts = [];
        foreach ($segments as $segment) {
            $segment = trim((string) $segment, '/');
            if ($segment !== '') {
                $parts[] = rawurlencode($segment);
            }
        }

        $url = rtrim($this->getEndPoint(), '/') . '/' . implode('/', $parts);

        if ($allowSynchronized && $this->getSynchronized()) { // any truthy value
            $url .= '?synchronized';
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->buildUrl([
            $this->getTypeOfRequest(),
            $this->getTransactionReference(),
            $this->getApiMethod(),
        ]);
    }

    /**
     * Operation body. Payload fields go in the JSON body, never in headers.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        if ($this->getAmount() !== null) {
            $data['amount'] = $this->getAmountInteger();
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setHttpMethod($method)
    {
        $this->httpMethod = $method;
        return $this;
    }

    /**
     * Sends a request to the Quickpay API.
     *
     * @return array{0: int, 1: string} HTTP status code and raw body
     */
    protected function sendRequest(string $method, string $url, ?array $body = null): array
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(':' . $this->getApikey()),
            'Accept-Version' => 'v10',
            'Accept' => 'application/json',
        ];
        if ($body !== null) {
            $headers['Content-Type'] = 'application/json';
        }
        if ($this->getNotifyUrl()) {
            $headers['QuickPay-Callback-Url'] = $this->getNotifyUrl();
        }

        $httpResponse = $this->httpClient->request(
            $method,
            $url,
            $headers,
            $body !== null ? json_encode((object) $body) : null
        );

        return [$httpResponse->getStatusCode(), (string) $httpResponse->getBody()];
    }

    /**
     * @param mixed $data
     * @return Response
     */
    public function sendData($data)
    {
        [$status, $body] = $this->sendRequest($this->getHttpMethod(), $this->getUrl(), is_array($data) ? $data : null);
        $class = $this->responseClass;
        return $this->response = new $class($this, $body, $status);
    }

    /**
     * @return mixed
     */
    public function send()
    {
        return $this->sendData($this->getData());
    }

    public function getTypeOfRequest()
    {
        return $this->getType() == 'subscription' ? 'subscriptions' : 'payments';
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setApiMethod($value)
    {
        $this->apimethod = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiMethod()
    {
        return $this->apimethod;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endpoint;
    }

    public function getTransactionReference()
    {
        $reference = parent::getTransactionReference();
        return ($reference === null || $reference === '') ? $reference : (string) $reference;
    }

    /**
     * @return int
     */
    public function getMerchant()
    {
        return $this->getParameter('merchant');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setMerchant($value)
    {
        return $this->setParameter('merchant', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setPrivatekey($value)
    {
        return $this->setParameter('privatekey', $value);
    }

    /**
     * @return string
     */
    public function getPrivatekey()
    {
        return $this->getParameter('privatekey');
    }

    /**
     * @return int
     */
    public function getAgreement()
    {
        return $this->getParameter('agreement');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setAgreement($value)
    {
        return $this->setParameter('agreement', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setApikey($value)
    {
        return $this->setParameter('apikey', $value);
    }

    /**
     * @return string
     */
    public function getApikey()
    {
        return $this->getParameter('apikey');
    }

    /**
     * @param $value
     * @return self
     */
    public function setSynchronized($value)
    {
        return $this->setParameter('synchronized', $value);
    }

    /**
     * @return boolean
     */
    public function getSynchronized()
    {
        return $this->getParameter('synchronized');
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * @return int
     */
    public function getDeadline()
    {
        return $this->getParameter('deadline');
    }

    /**
     * @param $value
     * @return int
     */
    public function setDeadline($value)
    {
        return $this->setParameter('deadline', $value);
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        return $this->getParameter('payment_methods');
    }

    /**
     * @param array $value
     * @return mixed
     */
    public function setPaymentMethods($value = array())
    {
        return $this->setParameter('payment_methods', $value);
    }

    /**
     * @return string
     */
    public function getGoogleAnalyticsTrackingID()
    {
        return $this->getParameter('google_analytics_tracking_id');
    }

    /**
     *
     * @param $value
     * @return mixed
     */
    public function setGoogleAnalyticsTrackingID($value)
    {
        return $this->setParameter('google_analytics_tracking_id', $value);
    }

    /**
     * @return string
     */
    public function getGoogleAnalyticsClientID()
    {
        return $this->getParameter('google_analytics_client_id');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setGoogleAnalyticsClientID($value)
    {
        return $this->setParameter('google_analytics_client_id', $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getParameter('description');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->getParameter('order_id');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setOrderID($value)
    {
        return $this->setParameter('order_id', $value);
    }

    /**
     * @param $value
     * @return self
     */
    public function setAutoCapture($value)
    {
        return $this->setParameter('auto_capture', $value);
    }

    /**
     * Null when not set, so each request can apply its own default.
     *
     * @return bool|null
     */
    public function getAutoCapture()
    {
        return $this->getParameter('auto_capture');
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->getParameter('variables');
    }

    /**
     * @param array $value
     * @return mixed
     */
    public function setVariables($value = array())
    {
        return $this->setParameter('variables', $value);
    }
}
