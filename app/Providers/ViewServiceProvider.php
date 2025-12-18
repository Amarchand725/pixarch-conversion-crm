<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\SidebarComposer;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer(
            'components.side-bar-menu',
            SidebarComposer::class
        );
    }
}