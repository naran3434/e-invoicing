<?php

namespace Naran3434\EInvoicing\GST;

use ErrorException;

class Response
{

    /**
     * @var
     */
    public $code;

    /**
     * @var
     */
    public $body;

    /**
     * @var
     */
    public $raw_body;

    /**
     * @var
     */
    public $headers;

    /**
     * @var
     */
    public $flag;

    /**
     * @param $response
     */
    public function __construct($response) {
        $this->code = $response->code;
        $this->body = $response->body;
        $this->raw_body = $response->raw_body;
        $this->headers = $response->headers;
        $this->flag = $response->body->flag;
    }

    /**
     * @return mixed
     */
    public function success(){
        return $this->body->flag;
    }

    /**
     * @return mixed
     */
    public function message(){
        return $this->body->message;
    }

    /**
     * @return null
     */
    public function fullAddress(){
        return $this->body->data->pradr->adr ?? null;
    }

    /**
     * @throws ErrorException
     */
    public function getAddress(): object {
        if($this->code !== 200)
            throw new ErrorException('Fetching data on unsuccessful request is not possible.');

        $address = $this->body->data->pradr->addr;
        return (object) [
            'floor_no'      => $address->flno ?? '',
            'building_no'   => $address->bnm ?? '',
            'street'        => $address->st ?? '',
            'location'      => $address->loc ?? '',
            'pin_code'      => $address->pncd ?? '',
            'city'          => $address->city ?? '',
            'state'         => $address->stcd ?? '',
            'district'      => $address->dst ?? '',
            'latitude'      => $address->lt ?? '',
            'longitude'     => $address->lg ?? ''
        ];
    }

    /**
     * @return mixed
     */
    public function tradeName() {
        return $this->body->data->tradeNam;
    }

    /**
     * @return mixed
     */
    public function legalName(){
        return $this->body->data->lgnm;
    }

    /**
     * @return mixed
     */
    public function isStatus(){
        return $this->body->data->sts;
    }
}