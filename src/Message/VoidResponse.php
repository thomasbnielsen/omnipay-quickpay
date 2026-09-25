<?php

namespace Omnipay\Quickpay\Message;

class VoidResponse extends Response
{
    protected $expectedOperation = 'cancel';
}
