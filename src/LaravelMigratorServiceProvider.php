<?php namespace Zingle\LaravelMigrator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\MigrationServiceProvider as LaravelServiceProvider;

class LaravelMigratorServiceProvider extends LaravelServiceProvider
{

    /**
     * Override the Laravel repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];
            return new DatabaseMigrationRepository($app['db'], $table);
        });
    }

    /**
     * Override the Laravel migrator singleton
     *
     * @return void
     */
    protected function registerMigrator()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Migrator($repository, $app['db'], $app['files']);
        });
    }

    /**
     * Override the Laravel Migrate command
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migrate', function ($app) {
            return new MigrateCommand($app['migrator']);
        });
    }      
}