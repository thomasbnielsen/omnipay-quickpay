<?php

namespace Omnipay\Quickpay\Message;

/**
 * Quickpay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    /**
     * @var string
     */
    protected $endpoint = 'https://api.link4pay.com';

    /**
     * @var string
     */
    private $apimethod = 'capture';

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = $this->getEndPoint() . $this->getTypeOfRequest() . '/' . $this->getTransactionReference(
            ) . '/' . $this->getApiMethod();
        if ($this->getSynchronized()) {
            $url .= '?synchronized';
            return $url;
        }
        return $url;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = array(
            'id'     => $this->getTransactionReference(),
            'amount' => $this->getAmountInteger()
        );
        return $data;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @param $data
     * @return mixed
     */
    public function sendData($data)
    {
        // TODO this seems to break api actions... what to do
        // prevent throwing exceptions for 4xx errors
        //$this->httpClient->getEventDispatcher()->addListener(
        //	'request.error',
        //	function ($event) {
        //		if ($event['response']->isClientError()) {
        //			$event->stopPropagation();
        //		}
        //	}
        //);

        $url = $this->getUrl();
        if (is_array($data) && array_key_exists('synchronized', $data)) {
            unset($data['synchronized']);
        }
        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $url,
            null,
            $data
        )->setHeader('Authorization', ' Basic ' . base64_encode(":" . $this->getApikey()))
            ->setHeader('Accept-Version', ' v10')
            ->setHeader('QuickPay-Callback-Url', $this->getNotifyUrl());


        $httpResponse = $httpRequest->send();

        return $this->response = new Response($this, $httpResponse->getBody(true));
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
        $type = 'payments';
        if ($this->getType() == 'subscription') {
            $type = 'subscriptions';
        }
        return $type;
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
        return boolval($this->getParameter('synchronized'));
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
}
