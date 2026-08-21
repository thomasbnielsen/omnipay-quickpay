<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * A plain resource deletion (e.g. DELETE /subscriptions/{id}) has no
 * documented success shape — unlike a payment operation (capture/void/
 * refund/recurring), it never returns an `operations` array, so
 * Response::isSuccessful() (which requires one) would always report false
 * here even on a genuine success. Quickpay's error shape (top-level
 * `message`/`error`) *is* documented and shared across the whole API, so
 * this treats "no error shape" as success rather than guessing at what a
 * successful delete's body looks like.
 */
class DeleteResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        $body = is_string($this->data) ? trim($this->data) : '';
        if ($body === '') {
            // A 204 No Content is a perfectly normal successful DELETE.
            return true;
        }

        $decoded = json_decode($body);
        if (!$decoded) {
            return true;
        }

        return !isset($decoded->message) && !isset($decoded->error);
    }

    public function getMessage()
    {
        $body = is_string($this->data) ? $this->data : '';
        $decoded = json_decode($body);
        if ($decoded && isset($decoded->message)) {
            $errors = isset($decoded->errors) ? ' ' . json_encode($decoded->errors) : '';
            return $decoded->message . $errors;
        }
        if ($decoded && isset($decoded->error)) {
            return (string) $decoded->error;
        }
        return '';
    }
}
