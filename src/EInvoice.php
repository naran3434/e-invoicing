<?php

namespace Naran3434\EInvoicing;

use Naran3434\EInvoicing\GST\GstIn;
use Naran3434\EInvoicing\IRN\GenerateIRN;

class EInvoice
{

    protected $instance;

    public function __construct() {

    }

    /**
     * @param $key
     * @return GstIn
     */
    public function gst($key): GstIn {
        return new GstIn($key);
    }

    /**
     * @param $headers
     * @return GenerateIRN
     */
    public function invoice($headers): GenerateIRN {
        return new GenerateIRN($headers);
    }
}