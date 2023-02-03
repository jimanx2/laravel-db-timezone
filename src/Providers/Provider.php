<?php

namespace Jimanx2\LaravelDbTimezone\Providers;

use Illuminate\Support\ServiceProvider;
use Jimanx2\LaravelDbTimezone\Observers\DateTimeObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Provider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Query DB timezone offset
        $offset = DB::select(DB::raw("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) AS `offset`"))[0]->offset;
        list($hrs, $mins, $secs) = explode(":", $offset);

        Config::set("database.connections.".config("database.default").".timezone", "+{$hrs}:{$mins}");
        Config::set("database.connections.".config("database.default").".timezone_rev", "-{$hrs}:{$mins}");
        
        // Register all model observers
        $filesInFolder = \File::files(app_path('Models'));

        foreach($filesInFolder as $path) {
            $modelClassName = "App\\Models\\".basename($path, '.php');
            $modelClassName::observe(DateTimeObserver::class);
        }
    }
}
