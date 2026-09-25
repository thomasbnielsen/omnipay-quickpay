<?php

namespace Omnipay\Quickpay\Message;

class CaptureResponse extends Response
{
    protected $expectedOperation = 'capture';
}
