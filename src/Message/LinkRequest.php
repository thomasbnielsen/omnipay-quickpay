<?php


namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class LinkRequest extends AbstractRequest
{
    public function setTransactionId($value)
    {
        $value = str_pad($value, 4, '0', STR_PAD_LEFT);
        if (strlen($value) > 24) {
            throw new InvalidRequestException('transactionId has a max length of 24');
        }
        return parent::setTransactionId($value);
    }

    /**
     * @return array
     */
    public function getQuickpayParams()
    {
        $params = array(
            'version'                      => 'v10',
            'agreement_id'                 => $this->getAgreement(),
            'order_id'                     => $this->getTransactionId(),
            'description'                  => $this->getDescription(),
            'amount'                       => $this->getAmountInteger(),
            'currency'                     => $this->getCurrency(),
            'continue_url'                 => $this->getReturnUrl(),
            'cancel_url'                   => $this->getCancelUrl(),
            'callback_url'                 => $this->getNotifyUrl(),
            'language'                     => $this->getLanguage(),
            'google_analytics_tracking_id' => $this->getGoogleAnalyticsTrackingID(),
            'google_analytics_client_id'   => $this->getGoogleAnalyticsClientID(),
            'auto_capture'                 => (bool) $this->getAutoCapture(),
        );

        // Unlike POST /payments (PurchaseRequest, which sends this
        // unconditionally and works fine either way), PUT /subscriptions/
        // {id}/link treats an empty payment_methods list as "no payment
        // method is allowed for this transaction" rather than "no
        // restriction" — surfaces on the hosted page itself as "No available
        // payment-method for transaction", not as an API-level error. Only
        // send it when the caller actually wants to restrict methods.
        if (!empty($this->getPaymentMethods())) {
            $params['payment_methods'] = $this->getPaymentMethods();
        }

        // Controls the hosted link page's own timeout (default 15 minutes)
        // — lets it be tied to whatever local deadline the caller is already
        // enforcing (e.g. an expiring booking), same as PurchaseRequest.
        if ($this->getDeadline() != '') {
            $params['deadline'] = $this->getDeadline();
        }

        return $params;
    }

    public function getData()
    {
        $this->validate('apikey', 'agreement', 'amount', 'transactionId', 'currency');

        return $this->getQuickpayParams();
    }


    public function send()
    {
        $fullData  = $this->getData();
        $reference = $this->getTransactionReference();

        if (!$reference) {
            // Omnipay's AbstractGateway::initialize() turns empty-array defaults into false.
            $variables = $this->getVariables();
            $data = [
                'order_id' => $fullData['order_id'],
                'currency' => $fullData['currency'],
            ];
            // POST /subscriptions requires a description; POST /payments accepts one.
            if (!empty($fullData['description'])) {
                $data['description'] = $fullData['description'];
            }
            if (!empty($variables)) {
                $data['variables'] = $variables;
            }

            [$status, $body] = $this->sendRequest('POST', $this->buildUrl([$this->getTypeOfRequest()], false), $data);
            $created = json_decode($body, true);
            if (!is_array($created) || !isset($created['id'])) {
                return $this->response = new LinkResponse($this, $body, null, $status);
            }
            $reference = (string) $created['id'];
        }

        unset($fullData['order_id'], $fullData['currency'], $fullData['description']);

        [$status, $body] = $this->sendRequest(
            'PUT',
            $this->buildUrl([$this->getTypeOfRequest(), $reference, 'link'], false),
            array_filter($fullData, function ($value) {
                return $value !== null && $value !== '';
            })
        );

        return $this->response = new LinkResponse($this, $body, $reference, $status);
    }
}
