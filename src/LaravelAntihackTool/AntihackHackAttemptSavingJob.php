<?php

namespace LaravelAntihackTool;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class AntihackHackAttemptSavingJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable;

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

    public function __construct($intruderIpAddress, $intruderUserAgent, $reason) {
        $this->intruderIpAddress = $intruderIpAddress;
        $this->intruderUserAgent = $intruderUserAgent;
        $this->reason = $reason;
    }

    public function handle() {
        AntihackServiceProvider::saveHackAttemptToDb($this->intruderIpAddress, $this->intruderUserAgent, $this->reason);
    }
}