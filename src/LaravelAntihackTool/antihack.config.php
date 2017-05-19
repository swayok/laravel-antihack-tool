<?php

return [

    /**
     * Enable/disable saving of hack attempts into DB
     */
    'store_hack_attempts' => env('ANTIHACK_STORE_HACK_ATTEMPTS', false),

    /**
     * Connection where hack attempts will be stored.
     * pgsql and mysql connections are supported. Redis support will be added later
     */
    'connection' => env('ANTIHACK_CONNECTION', 'default'),

    /**
     * Write hack attempts using queue job.
     * This will not overload your database when someone tries to hack you.
     */
    'use_queue' => env('ANTIHACK_USE_QUEUE', false),

    /**
     * Minimal number of hack attempts needed to ban IP
     */
    'ban_theshold' => env('ANTIHACK_BAN_THESHOLD', 20),
];