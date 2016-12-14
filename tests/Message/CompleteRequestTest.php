<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CompleteRequestTest extends TestCase
{
    /** @var  CompleteRequest */
    protected $request;
    /** @var  Request */
    protected $mockRequest;

    public function setUp()
    {
        parent::setUp();
        $this->mockRequest = $this->getHttpRequest();
        $this->request     = new CompleteRequest($this->getHttpClient(), $this->mockRequest);
    }

    public function testGetData()
    {
        $this->request->initialize(
            ['privateKey' => 1]
        );
        $data = $this->request->getData();
        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 0);
    }

    public function testGetDataWithContentTypeJson()
    {
        $this->mockRequest->initialize(
            ['test' => 1],
            [],
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE'             => 'application/json',
                'HTTP_QUICKPAY_CHECKSUM_SHA256' => '83ce13bda6523334daa14f33d1fc2adc98ff8b57c4df5be5d2f58b1c2c78fa1e'
            ],
            '[]'
        );

        $data = $this->request->getData();

        $this->assertEquals('[]', $data);
    }

    public function testGetDataWithContentTypeJsonWithInvalidSha()
    {
        $this->mockRequest->initialize(
            ['test' => 1],
            [],
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE'             => 'application/json',
                'HTTP_QUICKPAY_CHECKSUM_SHA256' => '83ce13dffa6523334daa14f33d1fc2adc98ff8b57c4df5be5d2f58b1c2c78fa1e'
            ],
            '[]'
        );

        try {
            $this->request->getData();
            $this->fail('Expected an exception');
        } catch (InvalidResponseException $e) {
            $this->assertEquals('Invalid response from payment gateway', $e->getMessage());
        }

    }


    public function testGetUrl()
    {
        $this->request->initialize(
            ['apikey' => 1, 'transactionId' => '10', 'agreement' => 2, 'amount' => 10.00, 'currency' => 'DKK']
        );
        $url = $this->request->getUrl();
        $this->assertNotContains('?synchronized', $url);

        foreach (['', 0, false, null] as $untruthyValue) {
            $this->request->initialize(
                [
                    'apikey'        => 1,
                    'transactionId' => '10',
                    'agreement'     => 2,
                    'amount'        => 10.00,
                    'currency'      => 'DKK',
                    'synchronized'  => $untruthyValue
                ]
            );
            $url = $this->request->getUrl();
            $this->assertNotContains('?synchronized', $url);
        }

        foreach (['1', 1, true, "synchronized"] as $truthyValue) {
            $this->request->initialize(
                [
                    'apikey'        => 1,
                    'transactionId' => '10',
                    'agreement'     => 2,
                    'amount'        => 10.00,
                    'currency'      => 'DKK',
                    'synchronized'  => $truthyValue
                ]
            );
            $url = $this->request->getUrl();
            $this->assertContains('?synchronized', $url);
        }


    }

}
