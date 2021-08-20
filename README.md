# omnipay-quickpay
An Omnipay driver for the Quickpay payment processor.

[QuickPay](https://quickpay.net/) is a Payment Service Provider that accept all common payment methods - credit cards, bank transfers, invoices, and more.

This package supports omnipay 3.x. For omnipay 2.x, see branch 1.0.

### Why Omnipay
The Omnipay php library is an easy to use, consistent payment processing library for PHP 5.3+
>Because you can learn one API and use it in multiple projects using different payment gateways
>Because if you need to change payment gateways you won't need to rewrite your code

Read more about Omnipay here: https://github.com/thephpleague/omnipay

### Supported Omnipay functionality
* Authorize
* Purchase
* Capture
* Refund
* Void
* Notification
* Recurring (Not supported by Omnipay, but Quickpay has made this functionality very similar to regular payments)

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

### Development
This module was coded by [Nobrainer Web](http://www.nobrainer.dk), with support from [QuickPay](https://quickpay.net/)
