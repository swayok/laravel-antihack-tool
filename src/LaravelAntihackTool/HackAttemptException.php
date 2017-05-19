<?php

namespace LaravelAntihackTool;

use Symfony\Component\HttpKernel\Exception\HttpException;

class HackAttemptException extends HttpException {

    protected $intruderIpAddress;

    /**
     * HackAttemptException constructor.
     * @param string $intruderIpAddress
     * @param \Exception|null $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct($intruderIpAddress, \Exception $previous = null, array $headers = array(), $code = 0) {
        /* HTTP 406 - Not Acceptable is best suited code IMHO */
        $message = 'Request cannot be accepted due to probability of harmful code in it. Continuous attemts may result in IP ban.';
        parent::__construct(406, $message, $previous, $headers, $code);
    }

    /**
     * @return string
     */
    public function getIntruderIpAddress() {
        return $this->intruderIpAddress;
    }

}