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

        // we have to flatten these arrays to fullfill html names, for form input fields.. (omnipay posts a form)
        if(!empty($this->getInvoiceAddress())){
            foreach($this->htmlFormNamesFromArray($this->getInvoiceAddress(), 'invoice_address') as $key => $val){
                $params[$key] = $val;
            }
        }

        if(!empty($this->getBasket())){
            foreach($this->htmlFormNamesFromArray($this->getBasket(), 'basket') as $key => $val){
                $params[$key] = $val;
            }
        }   

        // it seems description param is not always allowed, depending on the Type set
        if ($this->getDescription() != '') {
            $params['description'] = $this->getDescription();
        }

        return $params;
    }

    /**
     * Takes an associative or multi dim array like ['street' => 'value', 'city => 'value] and a prefix such as "invoice_address"
     * It then returns a array with values like 
     * ['invoice_address[street]' => 'value', 'invoice_address[city]' => 'value']
     * This is to fullfil the omnipay requirements, when it turns the params into html input fields
     * 
     * 
     * @param array $array
     * @param string $prefix
     * @return array
     */
    protected function htmlFormNamesFromArray($array, $prefix)
    {
        $flattened = [];
        foreach($array as $key => $val){
            if(is_array($val)){
                $deeperPrefix = $prefix . '[' . $key . ']';
                foreach($this->htmlFormNamesFromArray($val, $deeperPrefix) as $paramName => $paramValue){
                    $flattened[$paramName] = $paramValue;
                }
            } else {
                $flattened[$prefix . '[' . $key . ']'] = $val;   
            }
        }

        return $flattened;
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

        $data = $this->createChecksum($this->getQuickpayParams());

        return $data;
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
