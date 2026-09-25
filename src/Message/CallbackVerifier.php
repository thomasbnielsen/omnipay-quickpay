<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Checks that an incoming request is a genuine Quickpay callback.
 */
class CallbackVerifier
{
    public const CHECKSUM_HEADER = 'Quickpay-Checksum-Sha256';

    /**
     * True when the request carries a Quickpay callback body (JSON or checksum header).
     */
    public static function isCallback(Request $request): bool
    {
        $contentType = (string) $request->headers->get('Content-Type');
        return stripos($contentType, 'application/json') === 0
            || $request->headers->has(self::CHECKSUM_HEADER);
    }

    /**
     * Returns the raw callback body after verifying its HMAC-SHA256 checksum.
     *
     * @throws InvalidResponseException when the private key is missing or the checksum does not match
     */
    public static function verify(Request $request, $privateKey): string
    {
        if ($privateKey === null || $privateKey === '') {
            throw new InvalidResponseException('Quickpay private key is not configured; callback cannot be verified');
        }

        $body = (string) $request->getContent();
        $expected = hash_hmac('sha256', $body, (string) $privateKey);
        $given = (string) $request->headers->get(self::CHECKSUM_HEADER);

        if ($given === '' || !hash_equals($expected, $given)) {
            throw new InvalidResponseException('Invalid response from payment gateway');
        }

        return $body;
    }
}
