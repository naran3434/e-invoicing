<?php

namespace Naran3434\EInvoicing\IRN;

use Unirest\Request;

class GenerateIRN
{
    /**
     * @var array
     */
    protected $headers = array('Content-Type' => 'application/json');

    /**
     * @var string
     */
    protected $url = 'https://api.mygstcafe.com/eicore/v1.03/Invoice';

    /**
     * @param $headers
     */
    public function __construct($headers) {
        $this->headers = array_merge($this->headers, $headers);
    }


    /**
     * @param FormBuilder $builder
     * @return Response
     */
    public function generate(FormBuilder $builder): Response {
        // Disable verification only for this single call
        Request::curlOpt(CURLOPT_SSL_VERIFYPEER, false);
        Request::curlOpt(CURLOPT_SSL_VERIFYHOST, false);
        
        return new Response(Request::post($this->url, $this->headers, $builder->toJson()));
    }

}
