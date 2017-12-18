# What is this?
This is active anti-hack protection tool that will guard your project from most 
hack attempts that use HTTP requests. It also contains whitelist and blacklist 
functionality based on config files or database table contents

## Installation

Add `"swayok/laravel-antihack-tool": "master-dev"` to your `composer.json` 
into `require` section and run `composer update`

## Activation

### For Laravale 5.5+

Service provider will be automatically loaded

### For Laravel < 5.5

Add `LaravelAntihackTool\AntihackServiceProvider` to yor `app.providers` config

## Configuration

1. Run `php artisan vendor:publish --provider=LaravelAntihackTool\AntihackServiceProvider` 
to publish configuration file

2. If you plan to use database to store hack attempts - set `antihack.store_hack_attempts` 
configuration paramenter to `true` and modify `antihack.connection` and `antihack.table_name`
configuration paramenters if needed. Then run `php artisan antihack:install` and
confirm migration task. On production server you will need to run migration manually using
`php artisan migrate` command.

3. If you're using PeskyCmf or PeskyCms - you may need to add a menu item for resource in
your `menu()` method of your `AdminConfig` class (or other class that extends `CmfConfig` class). 
Menu item: `static::getMenuItem('hack_attempts')` or your custom one.

4. On development server you may need to set `antihack.allow_localhost_ip` to `true` in 
order to allow requests from `127.0.0.1` ip. By default this option is set to `true` for
`local` environment and to `false` for any other environments.

5. I you use any urls that have `.php` extensions - set `antihack.allow_php_extension_in_url` to `true`.

6. Whitelists and blacklists are generated automatically and cached to your default cache provider. 
You may change cache key and duration configuration paramenters if you need. Also you may update 
cache using `php artisan antihack:blacklist` manually