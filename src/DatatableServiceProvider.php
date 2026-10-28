<?php

namespace KatakNyulink\LivewireDatatable;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use KatakNyulink\LivewireDatatable\Livewire\Datatable;
use KatakNyulink\LivewireDatatable\Livewire\NestedDataTable;

class DatatableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datatable.php', 'datatable');
        // // Daftarkan komponen Livewire
        // Livewire::component('datatable', DataTable::class);

        // // Muat view
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'datatable');

        // // Publish config (opsional)
        // $this->publishes([
        //     __DIR__.'/../config/datatable.php' => config_path('datatable.php'),
        // ], 'datatable-config');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // $this->mergeConfigFrom(
        //     __DIR__.'/../config/datatable.php', 'datatable'
        // );

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'datatable');

        Livewire::component('datatable', DataTable::class);
        Livewire::component('nested-datatable', NestedDataTable::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datatable.php' => config_path('datatable.php'),
            ], 'datatable-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/datatable'),
            ], 'datatable-views');
        }
    }
}
