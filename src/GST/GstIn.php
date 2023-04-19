<?php

namespace Naran3434\EInvoicing\GST;

use http\Exception\InvalidArgumentException;
use Unirest\Request;
use Naran3434\EInvoicing\GST\Response as GSTResponse;

class GstIn
{

    /**
     * @var
     */
    protected $key;

    /**
     * @var string
     */
    protected $url = 'https://sheet.gstincheck.co.in/check/{apikey}/{gstin}';

    /**
     * @var string[]
     */
    protected $headers = array('Accept' => 'application/json');

    /**
     * @var
     */
    protected $gstin;

    /**
     * @var string
     */
    protected $regex = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/';

    /**
     * @var
     */
    protected $response;

    /**
     * @param String $key
     */
    public function __construct(String $key) {
        $this->key = $key;
    }


    /**
     * @param array $header
     * @return GstIn
     */
    public function setHeaders(Array $header): GstIn {
        $this->headers = array_merge($this->headers, $header);
        return $this;
    }

    /**
     * @param String $gstin
     * @return GstIn
     */
    public function setGst(String $gstin): GstIn {
        if(!preg_match($this->regex, $gstin)){
            throw new InvalidArgumentException("Please input proper GSTIN.");
        }
        $this->gstin = $gstin;
        return $this;
    }

    /**
     * @return void
     */
    protected function checkParams() {
        if(empty($this->gstin) || empty($this->key)){
            throw new InvalidArgumentException("Please input proper GSTIN.");
        }
    }

    /**
     * @return GSTResponse
     */
    public function get(): GSTResponse {
        $this->checkParams();
        $url = str_replace(['{apikey}', '{gstin}'], [$this->key, $this->gstin], $this->url);
        return new GSTResponse(Request::get($url, $this->headers));
    }


}