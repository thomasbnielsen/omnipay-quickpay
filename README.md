# omnipay-quickpay
A work in progress Omnipay driver for the Quickpay payment processor.

## Suggestions

* Add more docblocks to the class files.  This won't be accepted as an official omnipay gateway
  until everything is docblocked.  Documentation requirements have increased since omnipay became
  part of thephpleague.
* Explain in a little bit more detail about what the parameters are about.  Some of the Quickpay
  parameters are a little esoteric, e.g. I know what "apikey" should mean but what is "payment_window_apikey"?
* Use camelCase names for parameters internally and return them to snake_case names when you submit
  them to the gateway.  e.g. setPaymentWindowAgreement() should set an internal parmaeter called
  paymentWindowAgreement which is then translated to payment_window_agreement when it is sent to
  the gateway.
* I have added some FIXMEs to a few places.  You're on the right track but maybe have a look at
  some more completed gateways (e.g. PayPal REST, or Stripe) for some better ideas.  This appears
  to be a REST based gateway so find another REST gateway and copy the logic flow from that.
