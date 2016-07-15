<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /** @var  AuthorizeRequest */
    protected $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->initialize(
            ['apikey' => 1, 'transactionId' => '10', 'agreement' => 2, 'amount' => 10.00, 'currency' => 'DKK']
        );
        $data   = $this->request->getData();
        $fields = [
            'version'      => 'v10',
            'order_id'     => '0010',
            'agreement_id' => 2,
            'amount'       => '1000',
            'currency'     => 'DKK',
            'autocapture'  => 0,

        ];
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $data[$key], 'Key: ' . $key . ' not found in the data');
        }
        $this->assertTrue(strlen($data['order_id']) === 4);
    }

}
