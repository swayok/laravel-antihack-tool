<?php

return [
    /**
     * enable/disable protector (analyzes request to detect hack attempts)
     */
    'protector_enabled' => env('ANTIHACK_PROTECTOR_ENABLED', true),

    /**
     * enable/disable blacklister (blocks requests from ip addresses provided by 'connection' + 'table_name',
     * 'blacklisted_ip_addresses' and requests from 'blacklisted_user_agents')
     */
    'blacklister_enabled' => env('ANTIHACK_BLACKLISTER_ENABLED', true),

    /**
     * Allow/disallow 127.0.0.1 IP in $_SERVER['REMOTE_ADDR']
     */
    'allow_localhost_ip' => config('app.env') === 'local',

    /**
     * Allow/disallow '.php' string in URL address;
     * If disallowed - system will protect you from hacks like '/admin/index.php/some/url'
     * but it will also forbid '/index.php' and '/yourscript.php' URLs.
     * Allow this if you use such urls and load Laravel app with AntihackServiceProvider inside that scripts
     */
    'allow_php_extension_in_url' => false,

    /**
     * Enable/disable saving of hack attempts into DB
     */
    'store_hack_attempts' => env('ANTIHACK_STORE_HACK_ATTEMPTS', false),

    /**
     * Connection where hack attempts will be stored.
     * pgsql and mysql connections are supported.
     * Todo: Add Redis support for antihack protection
     */
    'connection' => env('ANTIHACK_CONNECTION'),

    /**
     * Name of DB table that stores hack attempts
     */
    'table_name' => 'hack_attempts',

    /**
     * Write hack attempts using queue job.
     * This will not overload your database when someone tries to hack you.
     */
    'use_queue' => env('ANTIHACK_USE_QUEUE', false),

    /**
     * Minimal number of hack attempts needed to ban IP
     */
    'ban_theshold' => env('ANTIHACK_BAN_THESHOLD', 20),

    /**
     * Cache key to store blacklisted IP addresses
     */
    'blacklist_cache_key' => 'antihack.blacklist',

    /**
     * How long blacklist will stay in cache (minutes)
     * If you want to store it forever: use 'forever' value
     */
    'blacklist_cache_duration' => 60,

    /**
     * List of additional IP addresses to add to blacklist
     */
    'blacklisted_ip_addresses' => [

    ],

    /**
     * List of allowed IP addresses even if they appear in blacklist
     */
    'whitelisted_ip_addresses' => [
        '127.0.0.1'
    ],

    /**
     * List of regexps that will blacklist specific user agents
     * Note: make sure you test your regexps against user agents modified by AntihackProtection::sanitizeUserAgent()
     */
    'blacklisted_user_agents' => [
        '%(research@pdrlabs\.net|Mozilla.*Jorgee$)%isu'
    ],

];