<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;


/**
 * quickpay Purchase Request
 * Docs can be found at http://tech.quickpay.net/payments/form/
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://payment.quickpay.net';

    /**
     * @return array
     */
    public function getQuickpayParams()
    {
        $params = array(
            "version"                      => "v10",
            "merchant_id"                  => $this->getMerchant(),
            "agreement_id"                 => $this->getAgreement(),
            "order_id"                     => $this->getTransactionId(),
            "amount"                       => $this->getAmountInteger(),
            "currency"                     => $this->getCurrency(),
            "continueurl"                  => $this->getReturnUrl(),
            "cancelurl"                    => $this->getCancelUrl(),
            "callbackurl"                  => $this->getNotifyUrl(),
            "language"                     => $this->getLanguage(),
            "google_analytics_tracking_id" => $this->getGoogleAnalyticsTrackingID(),
            "autocapture"                  => 1,
            "type"                         => $this->getType(),
            "payment_methods"              => $this->getPaymentMethods()
        );

        // it seems description param is not always allowed, depending on the Type set
        if ($this->getDescription() != '') {
            $params['description'] = $this->getDescription();
        }

        return $params;
    }

    public function setTransactionId($value)
    {
        $value = str_pad($value, 4, '0', STR_PAD_LEFT);
        if (strlen($value) > 24) {
            throw new InvalidRequestException('transactionId has a max length of 24');
        }
        return parent::setTransactionId($value);
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        // checks if any of these are empty, so we can throw an error without calling the API
        $this->validate('merchant', 'agreement', 'amount', 'transactionId');

        return $this->createChecksum($this->getQuickpayParams());
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createChecksum($data)
    {
        $data["checksum"] = $this->sign($data, $this->getApikey());
        return $data;
    }

    // taken from quickpays PHP example on how to calculate checksum for the payment form
    /**
     * @param $params
     * @param $api_key
     * @return string
     */
    public function sign($params, $api_key)
    {
        $flattened_params = $this->flatten_params($params);
        ksort($flattened_params);
        $base = implode(" ", $flattened_params);

        return hash_hmac("sha256", $base, $api_key);
    }

    /**
     * @param       $obj
     * @param array $result
     * @param array $path
     * @return array
     */
    public function flatten_params($obj, $result = array(), $path = array())
    {
        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $result = array_merge($result, $this->flatten_params($v, $result, array_merge($path, array($k))));
            }
        } else {
            $result[implode(
                "",
                array_map(
                    function ($p) {
                        return "[{$p}]";
                    },
                    $path
                )
            )] = $obj;
        }

        return $result;
    }

    /**
     * @param $data
     * @return mixed|PurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    /**
     * @return string|void
     */
    public function getHttpMethod()
    {
    }

}
