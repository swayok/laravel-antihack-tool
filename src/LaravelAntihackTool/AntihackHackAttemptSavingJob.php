<?php

namespace LaravelAntihackTool;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;

class AntihackHackAttemptSavingJob extends Job implements ShouldQueue {

    use InteractsWithQueue, Queueable;

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