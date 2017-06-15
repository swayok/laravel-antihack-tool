<?php

namespace LaravelAntihackTool;

use Illuminate\Console\Command;

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

    /**
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Cache\CacheManager $files
     */
    public function __construct($config, $cache) {
        $this->config = $config;
        $this->cache = $cache;
        parent::__construct();
    }

    public function fire() {
        $cacheKey = config('antihack.blacklist_cache_key', 'antihack.blacklist');
        $duration = (int)config('antihack.blacklist_cache_duration', 60);
        $blacklistedIps = [];
        if (config('antihack.store_hack_attempts')) {
            $blacklistedIps = \DB::connection(config('antihack.connection'))
                ->table(config('antihack.table_name'))
                ->select(['ip'])
                ->havingRaw('COUNT(*) > :treshold', ['treshold' => max((int)config('antihack.ban_theshold'), 1)])
                ->groupBy(['ip'])
                ->get(['id'])
                ->toArray();
        }
        if ($duration > 0) {
            \Cache::put($cacheKey, $blacklistedIps, $duration);
        } else {
            \Cache::forever($cacheKey, $blacklistedIps);
        }
    }
}