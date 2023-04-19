<?php

namespace Naran3434\EInvoicing\IRN;

use Unirest\Request;

class GenerateIRN
{
    /**
     * @var array
     */
    protected $headers = array('Accept' => 'application/json');

    /**
     * @var string
     */
    protected $url = 'https://testapi.mygstcafe.com/eicore/v1.03/Invoice';

    /**
     * @param $headers
     */
    public function __construct($headers) {
        $this->headers = array_merge($this->headers, $headers);
    }


    /**
     * @param $body
     * @return Response
     */
    public function generate($body): Response {
        return new Response(Request::post($this->url, $this->headers, $body));
    }

}