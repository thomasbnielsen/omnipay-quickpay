<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Hosted payment link. Successful (and a redirect) when Quickpay returned a URL.
 * getTransactionReference() is the payment/subscription id the link belongs to,
 * also when creating the link itself failed.
 */
class LinkResponse extends Response implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data, $reference = null, ?int $httpStatus = null)
    {
        parent::__construct($request, $data, $httpStatus, $reference === null ? null : (string) $reference);
    }

    public function isSuccessful()
    {
        $resource = $this->getResource();
        return $this->isHttpOk() && $resource && !empty($resource->url);
    }

    public function isPending()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        $resource = $this->getResource();
        return isset($resource->error) ? (string) $resource->error : null;
    }

    public function getTransactionReference()
    {
        return $this->reference;
    }

    public function isRedirect()
    {
        return $this->isSuccessful();
    }

    public function getRedirectUrl()
    {
        $resource = $this->getResource();
        return $resource->url ?? null;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return [];
    }
}
