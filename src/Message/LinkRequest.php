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
            'amount'                       => $this->getAmountInteger(),
            'currency'                     => $this->getCurrency(),
            'continue_url'                 => $this->getReturnUrl(),
            'cancel_url'                   => $this->getCancelUrl(),
            'callback_url'                 => $this->getNotifyUrl(),
            'language'                     => $this->getLanguage(),
            'google_analytics_tracking_id' => $this->getGoogleAnalyticsTrackingID(),
            'google_analytics_client_id'   => $this->getGoogleAnalyticsClientID(),
            'auto_capture'                 => $this->getAutoCapture(),
            'payment_methods'              => $this->getPaymentMethods(),
        );

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
            $url  = $this->getEndPoint() . '/' . $this->getTypeOfRequest() . '/';
            $variables = $this->getVariables();
            $data = [
                'order_id' => $fullData['order_id'],
                'currency' => $fullData['currency'],
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
                $reference = $response['id'];
            } else {
                return new LinkResponse($this, $body);
            }
        }
        unset($fullData['order_id']);
        unset($fullData['currency']);

        $url = $this->getEndPoint() . '/' . $this->getTypeOfRequest() . '/' . $reference . '/link';

        $httpResponse = $this->httpClient->request('PUT', $url, [
			'Authorization' => 'Basic ' . base64_encode(":" . $this->getApikey()),
			'Accept-Version' => 'v10',
			'Content-Type' => 'application/json',
			'QuickPay-Callback-Url' => $this->getNotifyUrl()
		], json_encode($fullData));

        return new LinkResponse($this, $httpResponse->getBody()->getContents(), $reference);
    }
}
