<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Tests\TestCase;

class CompleteRequestTest extends TestCase
{
    /** @var CompleteRequest */
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new CompleteRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    private function giveCallback(array $body, string $key = 'secret', ?string $checksum = null): string
    {
        $content = json_encode($body);
        $this->getHttpRequest()->initialize([], [], [], [], [], [
            'CONTENT_TYPE' => 'application/json; charset=utf-8',
            'HTTP_QUICKPAY_CHECKSUM_SHA256' => $checksum ?? hash_hmac('sha256', $content, $key),
        ], $content);
        return $content;
    }

    public function testBrowserReturnHasNoResult()
    {
        $this->getHttpRequest()->initialize(['foo' => 'bar']);
        $this->request->initialize(['privatekey' => 'secret']);
        $this->assertSame(['foo' => 'bar'], $this->request->getData());

        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertSame([], $this->getMockClient()->getRequests(), 'no API call on completion');
    }

    public function testApprovedPaymentCallbackIsSuccessfulWithoutApiCall()
    {
        $content = $this->giveCallback([
            'id' => 123456, 'order_id' => 'T100', 'accepted' => true, 'type' => 'Payment',
            'operations' => [['type' => 'authorize', 'pending' => false, 'qp_status_code' => '20000', 'qp_status_msg' => 'Approved']],
        ]);
        $this->request->initialize(['privatekey' => 'secret']);

        $response = $this->request->send();

        $this->assertSame($content, $response->getData());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('123456', $response->getTransactionReference());
        $this->assertSame('T100', $response->getTransactionId());
        $this->assertSame([], $this->getMockClient()->getRequests());
    }

    public function testSubscriptionAuthorisationCallback()
    {
        $this->giveCallback([
            'id' => 618586255, 'order_id' => 'S1', 'accepted' => true, 'type' => 'Subscription', 'state' => 'active',
            'metadata' => ['brand' => 'visa', 'last4' => '0008', 'exp_month' => 12, 'exp_year' => 2029],
            'operations' => [['type' => 'authorize', 'pending' => false, 'qp_status_code' => '20000', 'qp_status_msg' => 'Approved']],
        ]);
        $this->request->initialize(['privatekey' => 'secret']);

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('618586255', $response->getTransactionReference());
        $this->assertSame('Subscription', $response->getResourceType());
        $this->assertSame('0008', $response->getCardLast4());
        $this->assertSame('12/2029', $response->getCardExpiry());
    }

    public function testRejectedCallbackIsNotSuccessful()
    {
        $this->giveCallback([
            'id' => 1, 'accepted' => false,
            'operations' => [['type' => 'authorize', 'pending' => false, 'qp_status_code' => '30101', 'qp_status_msg' => 'SCA required on cards from EEA']],
        ]);
        $this->request->initialize(['privatekey' => 'secret']);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('30101', $response->getCode());
        $this->assertSame('authorize: SCA required on cards from EEA', $response->getMessage());
    }

    public function testWrongChecksumIsRejected()
    {
        $this->giveCallback(['id' => 1], 'secret', str_repeat('0', 64));
        $this->request->initialize(['privatekey' => 'secret']);

        $this->expectException(InvalidResponseException::class);
        $this->request->getData();
    }

    public function testMissingPrivateKeyIsRejected()
    {
        $this->giveCallback(['id' => 1], '');
        $this->request->initialize([]);

        $this->expectException(InvalidResponseException::class);
        $this->request->getData();
    }
}
