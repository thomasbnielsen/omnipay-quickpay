<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class LinkRequestTest extends TestCase
{
    /** @var  LinkRequest */
    protected $request;

    public function setUp()
    {
        parent::setUp();
        $this->request = new LinkRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'currency'     => 'DKK'
        ];
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $data[$key], 'Key: ' . $key . ' not found in the data');
        }
        $this->assertTrue(strlen($data['order_id']) === 4);
    }

    public function testTransactionId()
    {
        try {
            $this->request->initialize(['transactionId' => str_repeat('0', 25)]);
            $this->fail('Expecting an exception');
        } catch (InvalidRequestException $e) {
            $this->assertEquals('transactionId has a max length of 24', $e->getMessage());
        }
    }

    public function testSendingALinkRequest()
    {
        $this->setMockHttpResponse(['LinkRequestCreatePaymentSuccess.txt', 'LinkRequestCreatePaymentLinkSuccess.txt']);
        $this->request->initialize(
            ['apikey' => 1, 'transactionId' => '10', 'agreement' => 2, 'amount' => 10.00, 'currency' => 'DKK']
        );
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://test.link', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEquals('123123123', $response->getTransactionReference());
    }

    public function testSendingALinkRequestWithAnAlreadyExistingTransactionReference()
    {
        $this->setMockHttpResponse(['LinkRequestCreatePaymentLinkSuccess.txt']);
        $this->request->initialize(
            [
                'apikey'               => 1,
                'transactionId'        => '10',
                'agreement'            => 2,
                'amount'               => 10.00,
                'currency'             => 'DKK',
                'transactionReference' => 123123123
            ]
        );
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://test.link', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEquals('123123123', $response->getTransactionReference());
    }

    public function testSendingALinkRequestWithAnError()
    {
        $this->setMockHttpResponse(['LinkRequestCreatePaymentLinkFailure.txt']);
        $this->request->initialize(
            [
                'apikey'               => 1,
                'transactionId'        => '10',
                'agreement'            => 2,
                'amount'               => 10.00,
                'currency'             => 'DKK',
            ]
        );
        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
    }
    public function testSendingALinkRequestWithAnErrorOnTransaction()
    {
        $this->setMockHttpResponse(['LinkRequestCreatePaymentLinkFailure.txt']);
        $this->request->initialize(
            [
                'apikey'               => 1,
                'transactionId'        => '10',
                'agreement'            => 2,
                'amount'               => 10.00,
                'currency'             => 'DKK',
                'transactionReference' => 123123123
            ]
        );
        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
    }
}
