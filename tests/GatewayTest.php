<?php
namespace Omnipay\Quickpay;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Quickpay\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CaptureRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertTrue(count($request->getData()) > 0);
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Quickpay\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Quickpay\Message\RefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testVoid()
    {
        $request = $this->gateway->void();
        $this->assertInstanceOf('Omnipay\Quickpay\Message\VoidRequest', $request);
    }

    public function testLink()
    {
        $request = $this->gateway->link(['amount' => '10.00']);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\LinkRequest', $request);
    }

    public function testRecurring()
    {
        $request = $this->gateway->recurring(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\RecurringRequest', $request);
    }

    public function testNotify()
    {
        $request = $this->gateway->notify(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\NotifyRequest', $request);
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testCompleteVoid()
    {
        $request = $this->gateway->completeVoid(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testCompleteAuthorize()
    {
        $request = $this->gateway->completeAuthorize(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testCompleteCapture()
    {
        $request = $this->gateway->completeCapture(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testCompleteRefund()
    {
        $request = $this->gateway->completeRefund(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testCompleteRecurring()
    {
        $request = $this->gateway->completeRecurring(['amount' => 10.00]);
        $this->assertInstanceOf('Omnipay\Quickpay\Message\CompleteRequest', $request);
    }

    public function testAcceptNotification()
    {
        $notification = $this->gateway->acceptNotification();
        $this->assertInstanceOf('Omnipay\Quickpay\Message\Notification', $notification);
    }

}
