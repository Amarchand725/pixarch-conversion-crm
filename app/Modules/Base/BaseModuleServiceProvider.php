<?php

namespace App\Modules\Base;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    /**
     * Define your module name (must be overridden in child classes)
     */
    protected string $moduleName = '';

    /**
     * Define permissions to register (optional override)
     */
    protected array $permissions = [
        'view', 'create', 'edit', 'delete', 'restore', 'force-delete'
    ];

    /**
     * Define repository bindings (contract => implementation)
     */
    protected array $bindings = [];

    /**
     * Register dependencies like Repositories.
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Boot module permissions, observers, and logs.
     */
    public function boot(): void
    {
        $this->registerPermissions();
        $this->registerPolicies();
        $this->registerObservers();
        $this->logModuleLoaded();
    }

    // ------------------------------------------------------------
    // 🔗 Repository Bindings
    // ------------------------------------------------------------
    protected function registerBindings(): void
    {
        foreach ($this->bindings as $contract => $repository) {
            $this->app->bind($contract, $repository);
        }
    }

    // ------------------------------------------------------------
    // 🧾 Permissions (Spatie)
    // ------------------------------------------------------------
    protected function registerPermissions(): void
    {
        if (!$this->moduleName) {
            return;
        }

        foreach ($this->permissions as $perm) {
            Permission::findOrCreate(strtolower($this->moduleName) . '.' . $perm);
        }
    }

    // ------------------------------------------------------------
    // 🧭 Policies (optional override)
    // ------------------------------------------------------------
    protected function registerPolicies(): void
    {
        // To be overridden in module if needed
    }

    // ------------------------------------------------------------
    // 👁️ Observers (optional override)
    // ------------------------------------------------------------
    protected function registerObservers(): void
    {
        // To be overridden in module if needed
    }

    // ------------------------------------------------------------
    // 🧾 Activity Log
    // ------------------------------------------------------------
    protected function logModuleLoaded(): void
    {
        Log::channel('daily')->info("✅ {$this->moduleName} module booted successfully.");
    }
}
