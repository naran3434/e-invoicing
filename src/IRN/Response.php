<?php

namespace Naran3434\EInvoicing\IRN;

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
    public $status;

    /**
     * @param $response
     */
    public function __construct($response) {
        $this->code = $response->code;
        $this->body = $response->body;
        $this->raw_body = $response->raw_body;
        $this->headers = $response->headers;
        $this->status = $response->body->status_cd;
    }

    /**
     * @return mixed
     */
    public function irn(){
        return $this->body->response_data->Irn;
    }

}