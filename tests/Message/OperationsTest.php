<?php

namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Quickpay\Gateway;
use Omnipay\Tests\TestCase;
use Psr\Http\Message\RequestInterface;

class OperationsTest extends TestCase
{
    /** @var Gateway */
    protected $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(['apikey' => 'key', 'privatekey' => 'secret', 'merchant' => '1', 'agreement' => '2']);
    }

    private function lastRequest(): RequestInterface
    {
        $requests = $this->getMockClient()->getRequests();
        return end($requests);
    }

    private function body(RequestInterface $request): array
    {
        return json_decode((string) $request->getBody(), true) ?: [];
    }

    public function testRecurringSynchronousApproved()
    {
        $this->setMockHttpResponse('RecurringSyncApproved.txt');
        $response = $this->gateway->recurring([
            'type' => 'subscription', 'transactionReference' => 618586255, 'amount' => '250.00',
            'order_id' => 'R100-1', 'synchronized' => true, 'notifyUrl' => 'https://example.test/notify',
        ])->send();

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://api.quickpay.net/subscriptions/618586255/recurring?synchronized', (string) $request->getUri());
        $this->assertSame(['amount' => 25000, 'order_id' => 'R100-1', 'auto_capture' => true], $this->body($request));
        $this->assertSame('https://example.test/notify', $request->getHeaderLine('QuickPay-Callback-Url'));
        $this->assertFalse($request->hasHeader('amount'));

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('700001', $response->getTransactionReference());
        $this->assertSame('0008', $response->getCardLast4());
        $this->assertSame('capture: Approved', $response->getMessage());
    }

    public function testRecurringAcceptsOrderIdCamelCaseAndAutoCaptureOff()
    {
        $this->setMockHttpResponse('RecurringSyncApproved.txt');
        $this->gateway->recurring([
            'transactionReference' => '1', 'amount' => '1.00', 'orderId' => 'CB5', 'auto_capture' => false,
        ])->send();

        $request = $this->lastRequest();
        $this->assertSame('https://api.quickpay.net/subscriptions/1/recurring', (string) $request->getUri());
        $this->assertSame(['amount' => 100, 'order_id' => 'CB5', 'auto_capture' => false], $this->body($request));
    }

    public function testRecurringPending()
    {
        $this->setMockHttpResponse('RecurringAsyncPending.txt');
        $response = $this->gateway->recurring(['transactionReference' => '1', 'amount' => '250.00', 'orderId' => 'R100-2'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isPending());
        $this->assertSame('700002', $response->getTransactionReference());
    }

    public function testRecurringDeclined()
    {
        $this->setMockHttpResponse('RecurringDeclined.txt');
        $response = $this->gateway->recurring(['transactionReference' => '1', 'amount' => '250.00', 'orderId' => 'R100-3'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('40000', $response->getCode());
        $this->assertSame('recurring: Rejected test operation', $response->getMessage());
    }

    public function testRecurringValidationErrorDoesNotThrow()
    {
        $this->setMockHttpResponse('ValidationError.txt');
        $response = $this->gateway->recurring(['transactionReference' => '1', 'amount' => '250.00', 'orderId' => 'dup'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('Validation error {"order_id":["has already been taken"]}', $response->getMessage());
        $this->assertSame('400', $response->getCode());
    }

    public function testRecurringRequiresOrderId()
    {
        $this->expectException(InvalidRequestException::class);
        $this->gateway->recurring(['transactionReference' => '1', 'amount' => '1.00'])->getData();
    }

    public function testRefundSynchronous()
    {
        $this->setMockHttpResponse('RefundSyncApproved.txt');
        $response = $this->gateway->refund(['transactionReference' => '800001', 'amount' => '100.00', 'synchronized' => true])->send();

        $request = $this->lastRequest();
        $this->assertSame('https://api.quickpay.net/payments/800001/refund?synchronized', (string) $request->getUri());
        $this->assertSame(['amount' => 10000], $this->body($request));
        $this->assertFalse($request->hasHeader('id'));
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('800001', $response->getTransactionReference());
    }

    public function testRefundPendingAndRejected()
    {
        $this->setMockHttpResponse(['RefundPending.txt', 'RefundRejected.txt']);

        $pending = $this->gateway->refund(['transactionReference' => '800001', 'amount' => '100.00'])->send();
        $this->assertFalse($pending->isSuccessful());
        $this->assertTrue($pending->isPending());

        $rejected = $this->gateway->refund(['transactionReference' => '800001', 'amount' => '100.00'])->send();
        $this->assertFalse($rejected->isSuccessful(), 'a declined refund must not count as success');
        $this->assertFalse($rejected->isPending());
        $this->assertSame('refund: Rejected test operation', $rejected->getMessage());
    }

    public function testCapture()
    {
        $this->setMockHttpResponse('CaptureApproved.txt');
        $response = $this->gateway->capture(['transactionReference' => '800002', 'amount' => '100.00'])->send();
        $this->assertSame('https://api.quickpay.net/payments/800002/capture', (string) $this->lastRequest()->getUri());
        $this->assertSame(['amount' => 10000], $this->body($this->lastRequest()));
        $this->assertTrue($response->isSuccessful());
    }

    public function testCancelSubscriptionViaVoid()
    {
        $this->setMockHttpResponse('SubscriptionCancelApproved.txt');
        $response = $this->gateway->void(['type' => 'subscription', 'transactionReference' => '600001', 'synchronized' => true])->send();

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://api.quickpay.net/subscriptions/600001/cancel?synchronized', (string) $request->getUri());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('cancelled', $response->getState());
    }

    public function testNonJsonErrorIsNotSuccess()
    {
        $this->setMockHttpResponse('MethodNotAllowed405.txt');
        $response = $this->gateway->void(['type' => 'subscription', 'transactionReference' => '1'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('405', $response->getCode());
        $this->assertStringStartsWith('HTTP 405:', $response->getMessage());
    }

    public function testFetchSubscription()
    {
        $this->setMockHttpResponse('SubscriptionFetch.txt');
        $response = $this->gateway->fetchTransaction(['type' => 'subscription', 'transactionReference' => '600002', 'synchronized' => true])->send();

        $request = $this->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://api.quickpay.net/subscriptions/600002', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('active', $response->getState());
        $this->assertSame('visa', $response->getCardBrand());
        $this->assertSame('0008', $response->getCardLast4());
        $this->assertSame(12, $response->getCardExpiryMonth());
        $this->assertSame(2029, $response->getCardExpiryYear());
    }

    public function testFetchNotFound()
    {
        $this->setMockHttpResponse('NotFound404.txt');
        $response = $this->gateway->fetchTransaction(['type' => 'subscription', 'transactionReference' => '1'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Not found: No Subscription with id 1', $response->getMessage());
    }

    public function testSubscriptionLinkCreatesThenLinks()
    {
        $this->setMockHttpResponse(['SubscriptionCreated.txt', 'LinkRequestCreatePaymentLinkSuccess.txt']);
        $response = $this->gateway->link([
            'type' => 'subscription', 'transactionId' => 'S302', 'description' => 'Card on file',
            'amount' => '1.00', 'currency' => 'DKK', 'returnUrl' => 'https://example.test/ok',
            'cancelUrl' => 'https://example.test/cancel', 'notifyUrl' => 'https://example.test/notify',
        ])->send();

        [$create, $link] = $this->getMockClient()->getRequests();
        $this->assertSame('https://api.quickpay.net/subscriptions', (string) $create->getUri());
        $this->assertSame(['order_id' => 'S302', 'currency' => 'DKK', 'description' => 'Card on file'], $this->body($create));
        $this->assertSame('PUT', $link->getMethod());
        $this->assertSame('https://api.quickpay.net/subscriptions/600003/link', (string) $link->getUri());
        $this->assertArrayNotHasKey('payment_methods', $this->body($link));
        $this->assertSame(100, $this->body($link)['amount']);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('http://test.link', $response->getRedirectUrl());
        $this->assertSame('600003', $response->getTransactionReference());
    }

    public function testLinkWithNonJsonReplyDoesNotCrash()
    {
        $this->setMockHttpResponse('MethodNotAllowed405.txt');
        $response = $this->gateway->link(['transactionId' => 'S1', 'amount' => '1.00', 'currency' => 'DKK', 'type' => 'subscription', 'description' => 'x'])->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }
}
