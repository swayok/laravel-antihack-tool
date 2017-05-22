<?php

namespace LaravelAntihackTool;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\NullOutput;

class AntihackServiceProvider extends ServiceProvider {

    use DispatchesJobs;

    public function boot() {
        if (!$this->app->runningInConsole()) {
            if (config('antihack.blacklister_enabled', true)) {
                $this->runBlacklister();
            }
            if (config('antihack.protector_enabled', true)) {
                $this->runProtector();
            }
        }
    }

    public function register() {
        $this->app->singleton(
            'command.antihack.install',
            function ($app) {
                return new AntihackInstallCommand($app['config'], $app['files']);
            }
        );

        $this->app->singleton(
            'command.antihack.blacklist',
            function ($app) {
                return new AntihackBlacklistCommand($app['config'], $app['cache']);
            }
        );

        $this->commands('command.antihack.install', 'command.antihack.blacklist');

        $this->publishes([
            __DIR__ . DIRECTORY_SEPARATOR . 'antihack.config.php' => config_path('antihack.php')
        ], 'config');
    }

    protected function runBlacklister() {
        $userAgent = AntihackProtection::getUserAgent();
        foreach ((array)config('antihack.blacklisted_user_agents') as $regexp) {
            if (preg_match($regexp, $userAgent)) {
                abort(403, 'Your user agent is not allowed');
            }
        }
        $ip = AntihackProtection::getClientIp();
        if (
            !in_array($ip, (array)config('whitelisted_ip_addresses', []), true)
            && (
                in_array($ip, (array)config('blacklisted_ip_addresses', []), true)
                || in_array($ip, $this->getBlacklistedIpAddresses(), true)
            )
        ) {
            abort(403, 'Your IP address was blocked');
        }
    }

    protected function runProtector() {
        $allowLocalhostIp = config('antihack.allow_localhost_ip', false);
        $allowPhpExtensionInUrl = config('antihack.allow_php_extension_in_url', false);
        if (config('antihack.store_hack_attempts', false)) {
            try {
                AntihackProtection::run($allowPhpExtensionInUrl, $allowLocalhostIp);
            } catch (HackAttemptException $exc) {
                $this->saveExceptionToDb($exc);
                throw new $exc;
            }
        } else {
            AntihackProtection::run($allowPhpExtensionInUrl, $allowLocalhostIp);
        }
    }

    /**
     * @param HackAttemptException $exc
     */
    protected function saveExceptionToDb(HackAttemptException $exc) {
        if (config('antihack.use_queue')) {
            $this->dispatch(
                new AntihackHackAttemptSavingJob($exc->getIntruderIpAddress(), $exc->getIntruderUserAgent())
            );
        } else {
            static::saveHackAttemptToDb($exc->getIntruderIpAddress(), $exc->getIntruderUserAgent());
        }
    }

    /**
     * @return array
     */
    protected function getBlacklistedIpAddresses() {
        $cacheKey = config('antihack.blacklist_cache_key', 'antihack.blacklist');
        if (!\Cache::has($cacheKey)) {
            \Artisan::call('antihack:blacklist', [], new NullOutput);
        }
        return (array)\Cache::get($cacheKey, []);
    }

    /**
     * @param string|null $getIntruderIpAddress
     * @param string|null $getIntruderUserAgent
     */
    static public function saveHackAttemptToDb($getIntruderIpAddress, $getIntruderUserAgent) {
        \DB::connection(config('antihack.connection'))
            ->table(config('antihack.table_name'))
            ->insert([
                'ip' => $getIntruderIpAddress,
                'user_agent' => $getIntruderIpAddress,
            ]);
    }

}