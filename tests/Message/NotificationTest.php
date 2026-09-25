<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Tests\TestCase;

class NotificationTest extends TestCase
{
    /** @var Notification */
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new Notification($this->getHttpRequest(), '123');
    }

    private function giveCallback(array $data, ?string $checksum = null): void
    {
        $content = json_encode($data);
        $this->getHttpRequest()->initialize([], [], [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_QUICKPAY_CHECKSUM_SHA256' => $checksum ?? hash_hmac('sha256', $content, '123'),
        ], $content);
    }

    public function testGetPrivateKey()
    {
        $this->assertEquals('123', $this->notification->getPrivateKey());
        $this->assertEquals('1234', $this->notification->setPrivateKey('1234')->getPrivateKey());
    }

    public function testCompleted()
    {
        $this->giveCallback(['id' => 1, 'order_id' => '0010', 'operations' => [['type' => 'refund', 'qp_status_msg' => 'Approved', 'pending' => false, 'qp_status_code' => 20000]]]);

        $this->assertSame('Approved', $this->notification->getMessage());
        $this->assertSame('1', $this->notification->getTransactionReference());
        $this->assertSame('0010', $this->notification->getTransactionId());
        $this->assertSame(NotificationInterface::STATUS_COMPLETED, $this->notification->getTransactionStatus());
    }

    public function testPending()
    {
        $this->giveCallback(['id' => 1, 'operations' => [['type' => 'capture', 'qp_status_msg' => null, 'pending' => true]]]);
        $this->assertSame(NotificationInterface::STATUS_PENDING, $this->notification->getTransactionStatus());
    }

    public function testFailed()
    {
        $this->giveCallback(['id' => 1, 'operations' => [['type' => 'refund', 'qp_status_msg' => 'Rejected', 'pending' => false, 'qp_status_code' => '40000']]]);
        $this->assertSame(NotificationInterface::STATUS_FAILED, $this->notification->getTransactionStatus());
    }

    public function testInvalidChecksum()
    {
        $this->giveCallback(['id' => 1], 'dasdasdasdasdas');
        $this->expectException(InvalidResponseException::class);
        $this->notification->getData();
    }

    public function testNotACallback()
    {
        $this->assertSame('', $this->notification->getMessage());
        $this->assertNull($this->notification->getTransactionReference());
        $this->assertSame(NotificationInterface::STATUS_FAILED, $this->notification->getTransactionStatus());
    }
}
