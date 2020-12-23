<?php

namespace Rockbuzz\LaraMemberships;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;

class ServiceProvider extends SupportServiceProvider
{

    public function boot(Filesystem $filesystem)
    {
        $projectPath = database_path('migrations') . '/';
        $localPath = __DIR__ . '/database/migrations/';

        if (! $this->hasMigrationInProject($projectPath, $filesystem)) {
            $this->loadMigrationsFrom($localPath . '2020_12_17_000000_create_memberships_tables.php');

            $this->publishes([
                $localPath . '2020_12_17_000000_create_memberships_tables.php' =>
                    $projectPath . now()->format('Y_m_d_his') . '_create_memberships_tables.php'
            ], 'migrations');
        }

        $this->publishes([
            __DIR__ . '/config/memberships.php' => config_path('memberships.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/memberships.php', 'memberships');

        RBAC::createFromArray(config('memberships.rbac'));
    }

    private function hasMigrationInProject(string $path, Filesystem $filesystem)
    {
        return count($filesystem->glob($path . '*_create_memberships_tables.php')) > 0;
    }
}
