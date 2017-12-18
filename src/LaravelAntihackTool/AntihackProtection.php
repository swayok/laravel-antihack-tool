<?php

namespace LaravelAntihackTool;

use LaravelAntihackTool\Exception\HackAttemptException;

abstract class AntihackProtection {

    const BAD_DATA_REGEXP = '%(<\?php|cgi-bin|php://|\?>|wget\s+|file_get_contents|system\s*\(|chmod\s+\(|chmod\s+-r|chmod\s+-\d\d\d|sys_get_temp_dir|suhosin|echo\(|echo\s+[\(\'"`])%i';
    const IP_VALIDATION_REGEXP = '%^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$%';
    const BAD_URI_REGEXP = '%(^(/+admin)?/+app($|/)|/cgi_|\.(do|py)$)%i';
    const USER_AGENT_SANITIZER_REGEXP = '%[^a-zA-Z0-9\s|\.\-:_=+*/)(&?><^$#@\'";]+%is';

    /**
     * @param bool $allowLocalhostIp - true: allow client's IP address to be 127.0.0.1 (use it for localhost environment)
     * @throws \BadMethodCallException
     * @throws \UnexpectedValueException
     * @throws HackAttemptException
     */
    static public function run($allowPhpExtensionInUrl = false, $allowLocalhostIp = false) {
        if (!array_key_exists('REQUEST_URI', $_SERVER)) {
            throw new \BadMethodCallException(
                'AntihackProtection requires $_SERVER[\'REQUEST_URI\'] to be set.'
                    . ' Probably you\'re trying to run protection for a console command'
            );
        }
        $clientIp = static::getClientIp();
        $userAgent = static::getUserAgent();
        if (!empty($userAgent) && preg_match(static::BAD_DATA_REGEXP, $userAgent, $ret)) {
            throw new HackAttemptException($clientIp, $userAgent);
        }
        if (!preg_match(static::IP_VALIDATION_REGEXP, $clientIp)) {
            throw new HackAttemptException(null, $userAgent);
        }
        if (!$allowLocalhostIp && $clientIp === '127.0.0.1') {
            throw new \UnexpectedValueException(
                '$_SERVER[\'REMOTE_ADDR\'] is 127.0.0.1. Probably you need to reconfigure you web server or proxy to pass correct client\'s ip address'
            );
        }
        if (!empty($_SERVER['REQUEST_URI'])) {
            if (
                (
                    !$allowPhpExtensionInUrl
                    && stripos($_SERVER['REQUEST_URI'], '.php') !== false
                )
                || preg_match(static::BAD_URI_REGEXP, $_SERVER['REQUEST_URI'], $ret)
            ) {
                throw new HackAttemptException($clientIp, $userAgent);
            }
        }

        if (!empty($_GET) && preg_match(static::BAD_DATA_REGEXP, urldecode($_SERVER['QUERY_STRING']), $ret)) {
            throw new HackAttemptException($clientIp, $userAgent);
        }
        if (!empty($_POST) && preg_match(static::BAD_DATA_REGEXP, print_r($_POST, true), $ret)) {
            throw new HackAttemptException($clientIp, $userAgent);
        }
    }

    /**
     * @param string $userAgent
     * @return string
     */
    static public function sanitizeUserAgent($userAgent) {
        return trim(preg_replace(static::USER_AGENT_SANITIZER_REGEXP, '_', $userAgent));
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
}