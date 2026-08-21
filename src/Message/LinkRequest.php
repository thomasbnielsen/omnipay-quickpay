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
            'auto_capture'                 => $this->getAutoCapture(),
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
            $url  = rtrim($this->getEndPoint(), '/') . '/' . $this->getTypeOfRequest() . '/';
            $variables = $this->getVariables();
            $data = [
                'order_id' => $fullData['order_id'],
                'currency' => $fullData['currency'],
                // Required by POST /subscriptions — not documented as such
                // on POST /payments, which is why PurchaseRequest never
                // needed it.
                'description' => $fullData['description'],
            ];
            if (count($variables) > 0) {
                $data['variables'] = $variables;
            }

           $httpResponse = $this->httpClient->request('POST', $url, [
				'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
				'Accept-Version' => 'v10',
				'Content-Type' => 'application/json',
				'QuickPay-Callback-Url' => $this->getNotifyUrl()
			], json_encode($data));

            $body     = $httpResponse->getBody()->getContents();
            $response = json_decode($body, true);
            if (array_key_exists('id', $response)) {
                // Quickpay returns id as a JSON number — cast to string, or
                // SilverStripe 6's StringFieldValidator rejects it the
                // moment a caller writes it to a Varchar TransactionReference
                // (same class of bug as Response::getTransactionReference()).
                $reference = (string) $response['id'];
            } else {
                return new LinkResponse($this, $body);
            }
        }
        unset($fullData['order_id']);
        unset($fullData['currency']);
        unset($fullData['description']);

        $url = rtrim($this->getEndPoint(), '/') . '/' . $this->getTypeOfRequest() . '/' . $reference . '/link';

        $httpResponse = $this->httpClient->request('PUT', $url, [
			'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
			'Accept-Version' => 'v10',
			'Content-Type' => 'application/json',
			'QuickPay-Callback-Url' => $this->getNotifyUrl()
		], json_encode($fullData));

        return new LinkResponse($this, $httpResponse->getBody()->getContents(), $reference);
    }
}
