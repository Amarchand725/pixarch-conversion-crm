<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait ModuleInitializer
{
    protected string $prefix;
    protected string $routePrefix;
    protected string $pathInitialize;
    protected string $permissionPrefix;
    protected string $singularLabel;
    protected string $pluralLabel;

    /**
     * Initialize module variables
     */
    protected function initModule(string $name): void
    {
        $this->prefix = Str::kebab($name);
        $this->routePrefix = 'back-office.' . Str::plural($this->prefix);
        $this->pathInitialize = $this->routePrefix;
        $this->permissionPrefix = Str::snake($name);
        $cleanPrefix = Str::of($this->prefix)->replace('-', ' ')->trim();
        $this->singularLabel = Str::title($cleanPrefix);
        $this->pluralLabel = Str::title(Str::plural($cleanPrefix)) . ' List';
    }

    /**
     * Auto-detect module name from controller class
     */
    protected function autoInit(): void
    {
        $name = str_replace('Controller', '', class_basename(static::class));
        $this->initModule($name);
    }

    /**
     * Return module variables for Blade
     */
    protected function moduleViewVars(): array
    {
        return [
            'title' => $this->pluralLabel,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize' => $this->routePrefix,
            'singularLabel' => $this->singularLabel,
        ];
    }
}
