<?php

namespace LaravelAntihackTool\Exception;

class HackAttemptException extends AntihackException {

    /**
     * @var null|string
     */
    protected $intruderIpAddress;

    /**
     * @var null|string
     */
    protected $intruderUserAgent;

    /**
     * @var null|string
     */
    protected $reason;

    /**
     * HackAttemptException constructor.
     * @param string|null $intruderIpAddress - pass null when client's IP is invalid (fake ip or hack attempt)
     * @param string|null $userAgent - pass null when client's user agent is empty, not provided or contains only invalid symbols
     * @param string|null $reason - why this exception was thrown, what caused it
     * @param \Exception|null $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct($intruderIpAddress, $userAgent, $reason, \Exception $previous = null, array $headers = array(), $code = 0) {
        $this->intruderIpAddress = $intruderIpAddress;
        $this->intruderUserAgent = $userAgent;
        $this->reason = $reason;
        /* HTTP 406 - Not Acceptable is best suited code IMHO */
        $message = 'Request cannot be accepted due to probability of harmful code in it. Continuous attemts may result in IP ban.';
        parent::__construct(406, $message, $previous, $headers, $code);
    }

    /**
     * @return string|null - null means that client's IP is invalid (fake ip or hack attempt)
     */
    public function getIntruderIpAddress() {
        return $this->intruderIpAddress;
    }

    /**
     * @return string|null - null means that client's user agent is empty, not provided or contains only invalid symbols
     */
    public function getIntruderUserAgent() {
        return $this->intruderUserAgent;
    }

    /**
     * @return null|string
     */
    public function getReason() {
        return $this->reason;
    }

}