<?php

namespace LaravelAntihackTool\Exception;

class RequestBannedException extends AntihackException {

    public function __construct($message, \Exception $previous = null, array $headers = array(), $code = 0) {
        /* HTTP 423 - Locked is best suited code IMHO */
        parent::__construct(423, $message, $previous, $headers, $code);
    }
}