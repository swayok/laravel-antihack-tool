#What is this?
For example: you have a project that contains several very different sites in it. Let's say - it is Frontend, Admin and SuperAdmin sites. 
All these sites have different resources (views, css, js, images, etc.) but use common DB models, classes, etc. 
Separating them will be a bad idea and might waste a lot of time. 

##So what if we use Service Providers?
This can be done when you have a simple situation. But if there are some register() and provides() methods you will need to manually
detect if you need to load them or not, also you will need to do same verification for boot() method. Leaving this methods
without conditions may be dangerous when you have `$this->app->singleton('\Class\Name', callback)` calls on same `'\Class\Name'` but
with different `callback`. I've got many issues with it and finally made this classes to solve most of them as easy as possible.

##How to use it:

### 1. Create site loaders     
For each site in your project create a loader class that extends `\LaravelSiteLoader\AppSiteLoader`. (Or you can use 
`\LaravelSiteLoader\AppSiteLoaderInterface` to make you own version of `\LaravelSiteLoader\AppSiteLoader`

For frontend:

    namespace App\Frontend;
    
    use LaravelSiteLoader\AppSiteLoader;

    class FrontendSiteLoader extends AppSiteLoader {
    
        static public function canBeUsed() {
            return (
                $_SERVER['REQUEST_URI'] === '/'
                || empty($_SERVER['REQUEST_URI'])
                || starts_with($_SERVER['REQUEST_URI'], static::getBaseUrl())
            );
        }
    
        static public function getBaseUrl() {
            return '/account';
        }
        
        public function boot() {
            static::setLocale();
            // your code here
        }
        
        static public function getDefaultLocale() {
            return 'en';
        }
        
        public function register() {
            // your registrations here
        }
    
        public function provides() {
            // your privides list here
            return [];
        }
    }
        
For admin:
    
    namespace PeskyCMF\CMS\CmsAdmin;
    
    use LaravelSiteLoader\AppSiteLoader;

    class AdminSiteLoader extends AppSiteLoader {
    
        static public function getBaseUrl() {
            return '/admin';
        }
        
        public function boot() {
            static::setLocale();
            // your code here
        }
        
        static public function getDefaultLocale() {
            return 'en';
        }
        
        public function register() {
            // your registrations here
        }
    
        public function provides() {
            // your privides list here
            return [];
        }
    }

In `\LaravelSiteLoader\AppSiteLoader` there are some predefined fields:

    /** @var AppSitesServiceProvider */
    protected $provider;
    /** @var Application */
    protected $app;
    
and methods:

    static public function canBeUsed() {
        return (
            $_SERVER['REQUEST_URI'] === '/'
            || empty($_SERVER['REQUEST_URI'])
            || starts_with($_SERVER['REQUEST_URI'], static::getBaseUrl())
        );
    }
    
    /**
     * @return ParameterBag
     */
    protected function getAppConfig() {
        return config();
    }
    
    /**
     * Sets the locale if it exists in the session and also exists in the locales option
     *
     * @return void
     */
    static public function setLocale() {
        $locale = session()->get(get_called_class() . '_locale');
        \App::setLocale($locale ?: static::getDefaultLocale());
    }
    
    /**
     * Configure session for current site
     * @param string $connection - connection name
     * @param int $lifetime - session lifetime in minutes
     */
    public function configureSession($connection, $lifetime = 720) {
        $config = $this->getAppConfig()->get('session', ['table' => 'sessions', 'cookie' => 'session']);
        $this->getAppConfig()->set('session', array_merge($config, [
            'table' => $config['table'] . '_' . $connection,
            'cookie' => $config['cookie'] . '_' . $connection,
            'lifetime' => $lifetime,
            'connection' => $connection,
            'path' => static::getBaseUrl()
        ]));
    }
    
Overwrite them if you need something specific.

### 2. Create and configure special service provider

Create a single service provider that extends `\LaravelSiteLoader\Providers\AppSitesServiceProvider` and contains some
specific configs: `protected $defaultSectionLoaderClass` and `protected $sectionLoaderClasses`. 
Personally I use `AppServiceProvider` for this:


    namespace App\Providers;
    
    use LaravelSiteLoader\Providers\AppSitesServiceProvider;
    use App\Frontend\FrontendSiteLoader;
    use App\CmsAdmin\AdminSiteLoader;
    
    class AppServiceProvider extends AppSitesServiceProvider {
        protected $defaultSectionLoaderClass = FrontendSiteLoader::class;

        protected $sectionLoaderClasses = [
            AdminSiteLoader::class,
        ];
    }
    
Here:

- `protected $defaultSectionLoaderClass` is used whenever no loader from `protected $sectionLoaderClasses` can be used
- `protected $sectionLoaderClasses` list of all site loaders except default one

To detect correct loader `\LaravelSiteLoader\Providers\AppSitesServiceProvider` calls `AdminSiteLoader::canBeUsed()` method
for all loader calsses listed in `$this->sectionLoaderClasses`. The first loader that returns true will be assigned to 
`$this->siteLoader` property. If there is no matching loaders - `$this->defaultSectionLoaderClass` loader will be assigned to 
`$this->siteLoader` property without `canBeUsed()` method call

### 3. Add your service provider to `config/app.php`
    
##Notes
- In your ServiceProvider you can access matching loader via `$this->siteLoader` 
- If you overwrite `boot()`, `register()` or `provides()` methods in your ServiceProvider - make sure you call 
`parent::boot()`, `parent::register()` and `parent::provides()` methods within your methods to save loaders functionality
This code may be useful if you overload provides() method in service provider:


    public function provides() {
        return array_unique(array_merge(
            parent::provides(),
            [
                \App\Http\Request::class,
                DbModel::class
            ]
        ));
    } 
- In `\LaravelSiteLoader\Providers\AppSitesServiceProvider` I've rediclared some methods to be public:


    public function loadTranslationsFrom($path, $namespace)
    public function loadViewsFrom($path, $namespace)
    public function publishes(array $paths, $group = null)
    
So you can use them within loaders via `$this->provider->loadTranslationsFrom('/path', 'namespace')`