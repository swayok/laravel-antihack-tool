<?php

namespace LaravelAntihackTool;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\ServiceProvider;
use LaravelAntihackTool\Command\AntihackBlacklistCommand;
use LaravelAntihackTool\Command\AntihackInstallCommand;
use LaravelAntihackTool\Exception\AntihackException;
use LaravelAntihackTool\Exception\RequestBannedException;
use LaravelAntihackTool\PeskyCmf\CmfHackAttempts\CmfHackAttemptsScaffoldConfig;
use LaravelAntihackTool\Exception\HackAttemptException;
use PeskyCMF\Config\CmfConfig;

/**
 * @property Application $app
 */
class AntihackServiceProvider extends ServiceProvider {

    use DispatchesJobs;

    public function boot() {
        if (!$this->app->runningInConsole()) {
            try {
                if (config('antihack.blacklister_enabled', true)) {
                    $this->runBlacklister();
                }
                if (config('antihack.protector_enabled', true)) {
                    $this->runProtector();
                }
            } catch (AntihackException $exception) {
                $this->loadViewsAndTranslations();
                throw $exception;
            }
            if ($this->app->offsetExists('is_peskycmf_section') && $this->app->offsetGet('is_peskycmf_section')) {
                $this->loadViewsAndTranslations();
                $this->addSectionToPeskyCmfConfig();
            }
        }
    }

    protected function loadViewsAndTranslations() {
        $this->loadTranslationsFrom(__DIR__ . '/PeskyCmf/locale' , 'antihack');
        $this->loadViewsFrom(__DIR__ . '/PeskyCmf/views' , 'antihack');
    }

    public function register() {
        $loader = AliasLoader::getInstance();
        $loader->alias('Antihack', Antihack::class);

        $this->app->singleton(
            'command.antihack.install',
            function ($app) {
                return new AntihackInstallCommand($app['config'], $app['files']);
            }
        );

        $this->app->singleton(
            'command.antihack.blacklist',
            function () {
                return new AntihackBlacklistCommand();
            }
        );

        $this->commands('command.antihack.install', 'command.antihack.blacklist');

        $this->publishes([
            __DIR__ . DIRECTORY_SEPARATOR . 'antihack.config.php' => config_path('antihack.php')
        ], 'config');
    }

    /**
     * @throws RequestBannedException
     */
    protected function runBlacklister() {
        Antihack::protectFromBlacklistedRequesters(
            Antihack::getBlacklistedUserAgents(),
            Antihack::getBlacklistedIpAddresses()
        );
    }

    /**
     * @throws \BadMethodCallException
     * @throws \UnexpectedValueException
     * @throws \LaravelAntihackTool\Exception\HackAttemptException
     */
    protected function runProtector() {
        $allowLocalhostIp = config('antihack.allow_localhost_ip', false);
        $allowPhpExtensionInUrl = config('antihack.allow_php_extension_in_url', false);
        $whitelistedPhpScripts = Antihack::getWhitelistedPhpScripts();
        try {
            Antihack::analyzeRequestData($allowPhpExtensionInUrl, $whitelistedPhpScripts, $allowLocalhostIp);
        } catch (HackAttemptException $exc) {
            if (config('antihack.store_hack_attempts', false)) {
                $this->saveExceptionToDb($exc);
            }
            throw $exc;
        }
    }

    protected function addSectionToPeskyCmfConfig() {
        if (config('antihack.store_hack_attempts') && class_exists('\PeskyCMF\Config\CmfConfig')) {
            CmfConfig::getPrimary()->registerScaffoldConfigForResource(
                'hack_attempts',
                CmfHackAttemptsScaffoldConfig::class
            );
        }
    }

    /**
     * @param HackAttemptException $exc
     */
    protected function saveExceptionToDb(HackAttemptException $exc) {
        if (config('antihack.use_queue')) {
            $this->dispatch(
                new AntihackHackAttemptSavingJob($exc->getIntruderIpAddress(), $exc->getIntruderUserAgent(), $exc->getReason())
            );
        } else {
            static::saveHackAttemptToDb($exc->getIntruderIpAddress(), $exc->getIntruderUserAgent(), $exc->getReason());
        }
    }

    /**
     * @param string|null $getIntruderIpAddress
     * @param string|null $getIntruderUserAgent
     * @param string|null $reason
     */
    static public function saveHackAttemptToDb($getIntruderIpAddress, $getIntruderUserAgent, $reason) {
        \DB::connection(config('antihack.connection'))
            ->table(config('antihack.table_name'))
            ->insert([
                'ip' => $getIntruderIpAddress,
                'user_agent' => $getIntruderUserAgent,
                'reason' => $reason
            ]);
    }

}