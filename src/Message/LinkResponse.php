<?php


namespace Omnipay\Quickpay\Message;


use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

class LinkResponse extends Response implements RedirectResponseInterface
{
    protected $reference;

    public function __construct(RequestInterface $request, $data, $reference = null)
    {
        parent::__construct($request, $data);
        $this->reference = $reference;
    }

    public function isSuccessful()
    {
        $body = $this->getResponseBody();
        $body = json_decode($body);
        if (isset($body->url)) {
            return true;
        }
        return false;
    }

    public function getTransactionReference()
    {
        return $this->reference;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        $data = json_decode($this->getResponseBody());
        return $data->url;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getRedirectData()
    {
        return [];
    }


}
