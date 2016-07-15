<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class NotificationTest extends TestCase
{

    protected $request;
    /** @var  Notification */
    protected $notification;

    public function setUp()
    {
        parent::setUp();
        $this->request      = $this->getHttpRequest();
        $this->notification = new Notification($this->request, '123');
    }

    public function testGetPrivateKey()
    {
        $this->assertEquals('123', $this->notification->getPrivateKey());
        $this->assertEquals('1234', $this->notification->setPrivateKey('1234')->getPrivateKey());
    }

    public function testGetDataWithContentTypeJson()
    {
        $this->generateContent(
            [
                'id'         => 1,
                'order_id'   => '0010',
                'operations' => [['qp_status_msg' => 'test', 'pending' => false, 'qp_status_code' => 20000]]
            ]
        );

        $data = $this->notification->getMessage();
        $this->assertEquals('test', $data);
        $this->assertEquals(1, $this->notification->getTransactionReference());
        $this->assertEquals(NotificationInterface::STATUS_COMPLETED, $this->notification->getTransactionStatus());
    }

    public function testGetDataWithContentTypeJsonStatusPending()
    {
        $this->generateContent(
            [
                'id'         => 1,
                'order_id'   => '0010',
                'operations' => [['qp_status_msg' => 'test', 'pending' => true]]
            ]
        );

        $data = $this->notification->getMessage();
        $this->assertEquals('test', $data);
        $this->assertEquals(1, $this->notification->getTransactionReference());
        $this->assertEquals(NotificationInterface::STATUS_PENDING, $this->notification->getTransactionStatus());
    }

    public function testGetDataWithContentTypeJsonStatusFailed()
    {
        $this->generateContent(
            [
                'id'         => 1,
                'order_id'   => '0010',
                'operations' => [['qp_status_msg' => 'test', 'pending' => false, 'qp_status_code' => -1]]
            ]
        );

        $data = $this->notification->getMessage();
        $this->assertEquals('test', $data);
        $this->assertEquals(1, $this->notification->getTransactionReference());
        $this->assertEquals(NotificationInterface::STATUS_FAILED, $this->notification->getTransactionStatus());
    }

    public function testGetDataWithContentTypeJsonWithInvalidSha()
    {
        $this->request->initialize(
            ['test' => 1],
            [],
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE'             => 'application/json',
                'HTTP_QUICKPAY_CHECKSUM_SHA256' => 'dasdasdasdasdas'
            ],
            ''
        );


        try {
            $this->notification->getData();
            $this->fail('Expected an exception');
        } catch (InvalidResponseException $e) {
            $this->assertEquals('Invalid response from payment gateway', $e->getMessage());
        }

    }

    public function testNotificationOnInvalidRequest()
    {
        $this->assertEquals('', $this->notification->getMessage());
        $this->assertEquals(null, $this->notification->getTransactionReference());
    }

    private function generateContent($data)
    {
        $content = json_encode($data);
        $this->request->initialize(
            ['test' => 1],
            [],
            [],
            [],
            [],
            [
                'HTTP_CONTENT_TYPE'             => 'application/json',
                'HTTP_QUICKPAY_CHECKSUM_SHA256' => hash_hmac("sha256", $content, '123')
            ],
            $content
        );
    }

}
