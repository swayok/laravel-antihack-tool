<?php

namespace LaravelAntihackTool;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AntihackInstallCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'antihack:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel antihack (DB table migration)';

    /**
     * @var Filesystem
     */
    protected $files;
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct($config, Filesystem $files) {
        $this->config = $config;
        $this->files = $files;
        parent::__construct();
    }

    public function fire() {
        if ($this->config->get('antihack.store_hack_attempts', false)) {
            $migrationsPath = database_path('migrations') . DIRECTORY_SEPARATOR;
            $filePath = $migrationsPath . "2014_10_12_100000_create_table_hack_attempts.php";
            if ($this->files->exists($filePath)) {
                $this->line('- migration ' . $filePath . ' already exist. skipped.');
                return;
            }
            $className = 'Create' . $groupName . 'Table';
            $extendsClass = 'Cms' . $groupName . 'Migration';
            $fileContents = <<<FILE
    <?php 
    
    use LaravelAntihackTool\CreateTableHackAttemptsMigration;
    
    class CreateTableHackAttempts extends CreateTableHackAttemptsMigration {
    
    }

FILE;
            $this->files->put($filePath, $fileContents);
            $this->files->chmod($filePath, 0664);
            $this->line('Hach attempts table creation migration added');
        }
    }
}