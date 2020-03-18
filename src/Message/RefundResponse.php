<?php


namespace Omnipay\Quickpay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

class RefundResponse extends Response implements RedirectResponseInterface
{
    protected $reference;

    public function __construct(RequestInterface $request, $data, $reference = null)
    {
        parent::__construct($request, $data);
        $this->reference = $reference;
    }

    public function isSuccessful()
    {
        $data = json_decode($this->getResponseBody()); 

        /* If it is an object and it has the ID in it then the reference was found*/
        if (is_object($data) && isset($data->id)) {
            return true;
       }

       return false;
    }

    public function getTransactionReference()
    {
        return $this->reference;
    }

    /**
     * @return null|string
     */
    public function getCode(){
        $data = json_decode($this->getResponseBody());
        if ($this->isSuccessful()){
            if (is_array($data)) {  
                /* We have returned all of the transactions, we'll get the code from the most recent */
                $data = end($data);
            }
            $data = end($data->operations);
            return isset($data->qp_status_code) ? $data->qp_status_code : '';
        }
        return '404';
    }

   /**
     * @return null|string
     */
    public function getMessage(){
       $data = json_decode($this->getResponseBody());

        if ($this->isSuccessful()){
            if (is_array($data)) {  
                /* We have returned all of the transactions, we'll get the message from the most recent */
                $data = end($data);
            }
            $data = end($data->operations);
            return isset($data->qp_status_msg) ? $data->type . ': ' . $data->qp_status_msg : '';
        }

        return $data->message;
    }
 
   /**
     * @return null|string
     */
    public function getErrors(){
       $data = json_decode($this->getResponseBody());

        if ($this->isSuccessful()){
            if (is_array($data)) {  
                /* We have returned all of the transactions, we'll get the message from the most recent */
                $data = end($data);
            }
            $data = end($data->operations);
            return isset($data->qp_status_msg) ? $data->type . ': ' . $data->qp_status_msg : '';
        }

        return $data->errors;
    }
}
