<?php

namespace LaravelAntihackTool;

use LaravelAntihackTool\Exception\HackAttemptException;
use LaravelAntihackTool\Exception\RequestBannedException;

abstract class Antihack {

    const BAD_DATA_REGEXP = '%(<\?php|cgi-bin|php://|\?>|wget\s+|file_get_contents|system\s*\(|chmod\s+\(|chmod\s+-r|chmod\s+-\d\d\d|sys_get_temp_dir|suhosin|echo\(|echo\s+[\(\'"`])%i';
    const IPV4_VALIDATION_REGEXP = '%^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$%';
    const IPV6_VALIDATION_REGEXP = '%^([0-9a-f]{4}:){7}[0-9a-f]{4}$%i';
    const IPV4_AS_IPV6_VALIDATION_REGEXP = '%^::ffff:(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$%i';
    const BAD_URI_REGEXP = '%(^(/+admin)?/+app($|/)|/cgi_|\.(do|py)$)%i';
    const USER_AGENT_SANITIZER_REGEXP = '%[^a-zA-Z0-9\s|\.\-:_=+*/)(&?><^$#@\'";]+%i';

    const REASON_BAD_USER_AGENT = 'Bad User Agent';
    const REASON_INVALID_IP_ADDRESS = 'Invalid IP address';
    const REASON_PHP_EXTENSION_IN_URL = 'Attempt to access PHP file directly';
    const REASON_BAD_URL = 'Bad URL address';
    const REASON_BAD_URL_QUERY_DATA = 'Bad URL Query data';
    const REASON_BAD_POST_DATA = 'Bad POST data';

    /**
     * Analyze request data on subject of harmful code in it
     * Note: do not run this in command line script!
     * @param bool $allowPhpExtensionInUrl - false: any url with ".php" in path will be blocked
     * @param bool $allowLocalhostIp - true: allow client's IP address to be 127.0.0.1 (use it for localhost environment)
     * @throws \BadMethodCallException
     * @throws \UnexpectedValueException
     * @throws \LaravelAntihackTool\Exception\HackAttemptException
     */
    static public function analyzeRequestData($allowPhpExtensionInUrl = false, $allowLocalhostIp = false) {
        if (!array_key_exists('REQUEST_URI', $_SERVER)) {
            throw new \BadMethodCallException(
                'AntihackProtection requires $_SERVER[\'REQUEST_URI\'] to be set.'
                    . ' Probably you\'re trying to run protection for a console command'
            );
        }
        $clientIp = static::getClientIp();
        $userAgent = static::getUserAgent();
        if (!empty($userAgent) && preg_match(static::BAD_DATA_REGEXP, $userAgent, $ret)) {
            throw new HackAttemptException($clientIp, $userAgent, static::REASON_BAD_USER_AGENT);
        }
        if (
            !preg_match(static::IPV4_VALIDATION_REGEXP, $clientIp)
            && !preg_match(static::IPV6_VALIDATION_REGEXP, $clientIp)
            && !preg_match(static::IPV4_AS_IPV6_VALIDATION_REGEXP, $clientIp)
        ) {
            throw new HackAttemptException(null, $userAgent, static::REASON_INVALID_IP_ADDRESS);
        }
        if (!$allowLocalhostIp && $clientIp === '127.0.0.1') {
            throw new \UnexpectedValueException(
                '$_SERVER[\'REMOTE_ADDR\'] is 127.0.0.1. Probably you need to reconfigure you web server or proxy to pass correct client\'s ip address'
            );
        }
        if (!empty($_SERVER['REQUEST_URI'])) {
            if (!$allowPhpExtensionInUrl && stripos($_SERVER['REQUEST_URI'], '.php') !== false) {
                throw new HackAttemptException($clientIp, $userAgent, static::REASON_PHP_EXTENSION_IN_URL);
            }
            if (preg_match(static::BAD_URI_REGEXP, $_SERVER['REQUEST_URI'], $ret)) {
                throw new HackAttemptException($clientIp, $userAgent, static::REASON_BAD_URL);
            }
        }

        if (!empty($_GET) && preg_match(static::BAD_DATA_REGEXP, urldecode($_SERVER['QUERY_STRING']), $ret)) {
            throw new HackAttemptException($clientIp, $userAgent, static::REASON_BAD_URL_QUERY_DATA);
        }
        if (!empty($_POST) && preg_match(static::BAD_DATA_REGEXP, print_r($_POST, true), $ret)) {
            throw new HackAttemptException($clientIp, $userAgent, static::REASON_BAD_POST_DATA);
        }
    }

    /**
     * @return string
     * @throws \BadMethodCallException
     */
    static public function getClientIp() {
        static $ip;
        if ($ip === null) {
            if (empty($_SERVER['REMOTE_ADDR']) && empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                throw new \BadMethodCallException(
                    'AntihackProtection requires $_SERVER[\'REMOTE_ADDR\'] or $_SERVER[\'HTTP_X_FORWARDED_FOR\'] to be set.'
                        . ' Probably you\'re trying to run protection for a console command'
                );
            }
            $ip = empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @return null|string
     */
    static public function getUserAgent() {
        static $userAgent;
        if ($userAgent === null) {
            $userAgent = false;
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                $userAgent = static::sanitizeUserAgent($_SERVER['HTTP_USER_AGENT']);
                if (preg_match('%^[ _-]*$%', $userAgent)) {
                    $userAgent = false;
                }
            }
        }
        return $userAgent ?: null;
    }

    /**
     * @param string $userAgent
     * @return string
     */
    static public function sanitizeUserAgent($userAgent) {
        return trim(preg_replace(static::USER_AGENT_SANITIZER_REGEXP, '_', $userAgent));
    }

    /**
     * @param bool $ignoreCache
     * @return array
     */
    static public function getBlacklistedIpAddresses($ignoreCache = false) {
        $cacheKey = config('antihack.blacklist_cache_key', 'antihack.blacklist');
        if ($ignoreCache || !\Cache::has($cacheKey)) {
            $blacklistedIps = [];
            $whitelistedIps = static::getWhitelistedIpAddresses();
            if (config('antihack.store_hack_attempts')) {
                $query = \DB::connection(config('antihack.connection'))
                    ->table(config('antihack.table_name'))
                    ->select(['ip'])
                    ->whereNotNull('ip')
                    ->havingRaw(
                        'COUNT(*) >= ?',
                        [max((int)config('antihack.permanent_ban_theshold'), 1)]
                    )
                    ->groupBy(['ip']);
                if (count($whitelistedIps)) {
                    $query->whereNotIn('ip', $whitelistedIps);
                }
                $blacklistedIps = $query->get()->pluck('ip')->toArray();

                $tempBanTreshold = (int)config('antihack.temporary_ban_theshold');
                $tempBanDuration = (int)config('antihack.temporary_ban_duration');
                if ($tempBanTreshold > 0 && $tempBanDuration > 0) {
                    $query = \DB::connection(config('antihack.connection'))
                        ->table(config('antihack.table_name'))
                        ->select(['ip'])
                        ->whereNotNull('ip')
                        ->whereRaw(
                            'created_at >= ?',
                            [date('Y-m-d H:i:s', strtotime("-{$tempBanDuration} hours"))]
                        )
                        ->whereNotIn('ip', $whitelistedIps)
                        ->havingRaw(
                            'COUNT(*) >= ?',
                            [$tempBanTreshold,]
                        )
                        ->groupBy(['ip']);
                    if (count($whitelistedIps)) {
                        $query->whereNotIn('ip', $whitelistedIps);
                    }
                    $blacklistedIps = array_merge(
                        $blacklistedIps,
                        $query->get()->pluck('ip')->toArray()
                    );
                }

                $blacklistedIps = array_merge($blacklistedIps, static::getBlacklistedByConfigIpAddresses());
            }
            $duration = (int)config('antihack.blacklist_cache_duration', 60);
            if ($duration > 0) {
                \Cache::put($cacheKey, $blacklistedIps, $duration);
            } else {
                \Cache::forever($cacheKey, $blacklistedIps);
            }
            return $blacklistedIps;
        }
        return (array)\Cache::get($cacheKey);
    }

    /**
     * @return array
     */
    static public function getBlacklistedByConfigIpAddresses() {
        return (array)config('antihack.blacklisted_ip_addresses', []);
    }

    /**
     * @return array
     */
    static public function getWhitelistedIpAddresses() {
        return (array)config('antihack.whitelisted_ip_addresses', []);
    }

    /**
     * @return array
     */
    static public function getBlacklistedUserAgents() {
        return (array)config('antihack.blacklisted_user_agents', []);
    }

    /**
     * Test user agent and client ip if it is blacklisted and throw exception if it is
     * @param array $blacklistedUserAgents - regular expressions list
     * @param array $blacklistedIpAddresses - ip addresses list
     * @param null|string $clientUserAgent
     * @param null|string $clientIp
     * @throws \BadMethodCallException
     * @throws \LaravelAntihackTool\Exception\RequestBannedException
     */
    static public function protectFromBlacklistedRequesters(
        array $blacklistedUserAgents,
        array $blacklistedIpAddresses,
        $clientUserAgent = null,
        $clientIp = null
    ) {
        // check user agent
        $clientUserAgent = $clientUserAgent === null ? static::getUserAgent() : static::sanitizeUserAgent($clientUserAgent);
        if ($clientUserAgent) {
            foreach ($blacklistedUserAgents as $regexp) {
                if (preg_match($regexp, $clientUserAgent)) {
                    throw new RequestBannedException('Your user agent is not allowed.');
                }
            }
        }
        // check ip address
        if ($clientIp === null) {
            $clientIp = static::getClientIp();
        }
        if (in_array($clientIp, $blacklistedIpAddresses, true)) {
            throw new RequestBannedException('Your IP address was blocked.');
        }
    }
}