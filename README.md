# omnipay-quickpay
An Omnipay driver for the Quickpay payment processor.

[QuickPay](https://quickpay.net/) is a Payment Service Provider that accept all common payment methods - credit cards, bank transfers, invoices, and more.

This package supports omnipay 3.x. For omnipay 2.x, see branch 1.0.

### Why Omnipay
The Omnipay php library is an easy to use, consistent payment processing library for PHP 5.3+
>Because you can learn one API and use it in multiple projects using different payment gateways
>Because if you need to change payment gateways you won't need to rewrite your code

Read more about Omnipay here: https://github.com/thephpleague/omnipay

### Supported functionality
| Method | Quickpay call | Notes |
|---|---|---|
| `purchase` / `authorize` | Hosted payment window | Redirect response |
| `completePurchase` / `completeAuthorize` | none | Reads the checksum-verified callback body |
| `acceptNotification` | none | Callback for capture / refund / cancel |
| `capture` | `POST /payments/{id}/capture` | |
| `refund` | `POST /payments/{id}/refund` | |
| `void` | `POST /{payments\|subscriptions}/{id}/cancel` | With `type=subscription` this cancels a saved card |
| `link` | `POST /{type}` + `PUT /{type}/{id}/link` | Hosted link, e.g. to save a card as a subscription |
| `recurring` | `POST /subscriptions/{id}/recurring` | Charge a saved card. Needs `orderId`; `auto_capture` defaults to true |
| `fetchTransaction` | `GET /{payments\|subscriptions}/{id}` | Look up state and masked card details |

Set `type` to `subscription` to work with subscriptions (saved cards). Set `synchronized` to wait for Quickpay's final answer instead of a pending 202.

### Reading responses
* `isSuccessful()` - the operation was approved (qp_status_code 20000)
* `isPending()` - Quickpay is still processing it (asynchronous answer)
* otherwise it failed; `getMessage()` / `getCode()` say why (also for Quickpay error bodies and non-JSON replies)
* `getTransactionReference()` - payment or subscription id, always a string
* `getCardBrand()`, `getCardLast4()`, `getCardExpiry()` (`MM/YYYY`), `getMetadata()`, `getState()`, `getLatestOperation($type)`

### Upgrading to 5.0
* Callbacks are read from the callback body again (4.x made an API call that failed). A missing private key now rejects every callback.
* `delete()` is removed; Quickpay answers DELETE on subscriptions with 405. Use `void()` with `type=subscription`.
* `status()` is replaced by `fetchTransaction()` (`status()` remains as a deprecated alias).
* Refund honours `synchronized`, and a declined refund is no longer reported as successful.
* `recurring()` uses `auto_capture` (default true) and requires an order id.
* Calling a method the gateway does not have now fails instead of returning null.
* Requires PHP 8.1+.

### Supported Quickpay parameters
* type
* merchant
* agreement
* apikey
* privatekey
* language
* google_analytics_tracking_id
* google_analytics_client_id
* description
* order_id
* payment_methods
* synchronized (bool)
* deadline
* auto_capture (link, recurring)
* variables (link)

### Please note
If you need to do instant capture or auto_capture / [autocapture]([https://quickpay.net/](https://quickpay.net/dk/helpdesk/capture/#autocapture)) you should set the flag use_authorize: false. This will do a instant capture.
```
SilverStripe\Omnipay\GatewayInfo:
  Quickpay:
    use_authorize: true
```
Read more here: https://quickpay.net/dk/helpdesk/capture/#autocapture


### Development
Run the tests with `composer install && composer test` (PHP 8.2+ for PHPUnit).


This module was coded by [Nobrainer Web](http://www.nobrainer.dk), with support from [QuickPay](https://quickpay.net/)
