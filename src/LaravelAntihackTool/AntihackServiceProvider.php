<?php

namespace LaravelAntihackTool;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\ServiceProvider;
use LaravelAntihackTool\PeskyCmf\CmfHackAttempts\CmfHackAttemptsScaffoldConfig;
use LaravelAntihackTool\PeskyCmf\CmfHackAttempts\CmfHackAttemptsTable;
use PeskyCMF\Config\CmfConfig;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @property Application $app
 */
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
            $this->addSectionToPeskyCmfConfig();
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
                throw $exc;
            }
        } else {
            AntihackProtection::run($allowPhpExtensionInUrl, $allowLocalhostIp);
        }
    }

    protected function addSectionToPeskyCmfConfig() {
        if (config('antihack.store_hack_attempts') && class_exists('\PeskyCMF\Config\CmfConfig')) {
            $this->loadTranslationsFrom(__DIR__ . '/PeskyCmf/locale' , 'antihack');
            $cmfConfig = \PeskyCMF\Config\CmfConfig::getPrimary();
            $cmfConfig::addMenuItem('hack_attempts', function () {
                return [
                    'label' => trans('antihack::antihack.hack_attempts.menu_title'),
                    'url' => routeToCmfItemsTable('hack_attempts'),
                    'icon' => 'fa fa-shield'
                ];
            });
            $cmfConfig::registerDbTableAndScaffoldConfig(
                CmfHackAttemptsTable::getInstance(),
                CmfHackAttemptsScaffoldConfig::class,
                'hack_attempts'
            );
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
                'user_agent' => $getIntruderUserAgent,
            ]);
    }

}