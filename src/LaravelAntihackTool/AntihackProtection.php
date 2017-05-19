<?php

namespace LaravelAntihackTool;

abstract class AntihackProtection {

    /**
     * @param bool $throwException - true:
     * @return array - intruder info: ip, user_agent
     * @throws \BadMethodCallException
     * @throws \UnexpectedValueException
     * @throws \LaravelAntihackTool\HackAttemptException
     */
    static public function run($throwException = true) {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            throw new \BadMethodCallException(
                'AntihackProtection requires $_SERVER[\'REMOTE_ADDR\'] to be set. Probably you\'re trying to run it for a console command'
            );
        }
        $clientIp = $_SERVER['REMOTE_ADDR'];
        if ($clientIp === '127.0.0.1') {
            throw new \UnexpectedValueException(
                '$_SERVER[\'REMOTE_ADDR\'] is 127.0.0.1. Probably you need to reconfigure you web server or proxy to pass correct client\'s ip address'
            );
        }
        if (empty($_SERVER['REQUEST_URI'])) {
            throw new \BadMethodCallException(
                'AntihackProtection requires $_SERVER[\'REQUEST_URI\'] to be set. Probably you\'re trying to run it for a console command'
            );
        }
        $badUriRegexp = '%(^(/+admin)?/+app($|/)|/cgi_|\.(do|py)$)%i';
        if (!empty($_SERVER['REQUEST_URI']) && preg_match($badUriRegexp, $_SERVER['REQUEST_URI'], $ret)) {
            throw new HackAttemptException($clientIp);
        }

        $badDataRegexp = '%(<\?php|cgi-bin|php://|\?>|wget\s+|file_get_contents|system\s*\(|chmod\s+\(|chmod\s+-r|chmod\s+-\d\d\d|sys_get_temp_dir|suhosin|echo\(|echo\s+[\(\'"`])%i';
        if (!empty($_GET) && preg_match($badDataRegexp, urldecode($_SERVER['QUERY_STRING']), $ret)) {
            throw new HackAttemptException($clientIp);
        }
        if (!empty($_POST) && preg_match($badDataRegexp, print_r($_POST, true), $ret)) {
            throw new HackAttemptException($clientIp);
        }
        if (!empty($_SERVER['HTTP_USER_AGENT']) && preg_match($badDataRegexp, $_SERVER['HTTP_USER_AGENT'], $ret)) {
            throw new HackAttemptException($clientIp);
        }
    }
}