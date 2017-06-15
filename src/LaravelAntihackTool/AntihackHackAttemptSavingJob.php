<?php

namespace LaravelAntihackTool;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class AntihackHackAttemptSavingJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable;

    protected $intruderIpAddress;

    protected $intruderUserAgent;

    function __construct($intruderIpAddress, $intruderUserAgent) {
        $this->intruderIpAddress = $intruderIpAddress;
        $this->intruderUserAgent = $intruderUserAgent;
    }

    public function handle() {
        AntihackServiceProvider::saveHackAttemptToDb($this->intruderIpAddress, $this->intruderUserAgent);
    }
}