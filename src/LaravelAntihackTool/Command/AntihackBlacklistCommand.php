<?php

namespace LaravelAntihackTool\Command;

use Illuminate\Console\Command;
use LaravelAntihackTool\Antihack;

class AntihackBlacklistCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'antihack:blacklist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect blacklisted IP addresses and store into cache';

    public function handle() {
        Antihack::getBlacklistedIpAddresses(true);
    }

    public function fire() {
        $this->handle();
    }
}