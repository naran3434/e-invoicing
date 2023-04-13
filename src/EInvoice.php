<?php

namespace Naran3434\EInvoicing;

use Naran3434\EInvoicing\GST\GstIn;

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
}