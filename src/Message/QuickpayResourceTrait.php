<?php

namespace Omnipay\Quickpay\Message;

/**
 * Reads a Quickpay payment or subscription resource (API reply or callback body).
 * The using class provides getResource(): ?object.
 */
trait QuickpayResourceTrait
{
    /**
     * Latest operation, optionally of a given type (e.g. "refund", "capture", "cancel").
     *
     * @return object|null
     */
    public function getLatestOperation(?string $type = null)
    {
        $resource = $this->getResource();
        if (!$resource || empty($resource->operations) || !is_array($resource->operations)) {
            return null;
        }
        $operations = $resource->operations;
        for ($i = count($operations) - 1; $i >= 0; $i--) {
            $operation = $operations[$i];
            if ($type === null || (isset($operation->type) && $operation->type === $type)) {
                return $operation;
            }
        }
        return null;
    }

    /**
     * "Payment" or "Subscription".
     *
     * @return string|null
     */
    public function getResourceType()
    {
        $resource = $this->getResource();
        return $resource->type ?? null;
    }

    /**
     * Quickpay state, e.g. "initial", "new", "pending", "processed", "active", "cancelled", "rejected".
     *
     * @return string|null
     */
    public function getState()
    {
        $resource = $this->getResource();
        return $resource->state ?? null;
    }

    public function isAccepted(): bool
    {
        $resource = $this->getResource();
        return !empty($resource->accepted);
    }

    /**
     * Quickpay's order_id for the resource.
     *
     * @return string|null
     */
    public function getOrderId()
    {
        $resource = $this->getResource();
        return isset($resource->order_id) ? (string) $resource->order_id : null;
    }

    /**
     * Card metadata as an associative array (brand, last4, exp_month, exp_year, ...).
     */
    public function getMetadata(): ?array
    {
        $resource = $this->getResource();
        if (!$resource || empty($resource->metadata)) {
            return null;
        }
        return json_decode(json_encode($resource->metadata), true);
    }

    /**
     * @return string|null e.g. "visa", "mastercard", "dankort"
     */
    public function getCardBrand()
    {
        $meta = $this->getMetadata();
        return !empty($meta['brand']) ? (string) $meta['brand'] : null;
    }

    /**
     * @return string|null
     */
    public function getCardLast4()
    {
        $meta = $this->getMetadata();
        return !empty($meta['last4']) ? (string) $meta['last4'] : null;
    }

    /**
     * @return int|null
     */
    public function getCardExpiryMonth()
    {
        $meta = $this->getMetadata();
        return !empty($meta['exp_month']) ? (int) $meta['exp_month'] : null;
    }

    /**
     * @return int|null Four-digit year
     */
    public function getCardExpiryYear()
    {
        $meta = $this->getMetadata();
        return !empty($meta['exp_year']) ? (int) $meta['exp_year'] : null;
    }

    /**
     * @return string|null "MM/YYYY"
     */
    public function getCardExpiry()
    {
        $month = $this->getCardExpiryMonth();
        $year = $this->getCardExpiryYear();
        if (!$month || !$year) {
            return null;
        }
        return sprintf('%02d/%04d', $month, $year);
    }
}
