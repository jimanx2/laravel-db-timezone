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
        $this->mergeConfigFrom(
            __DIR__.'/../Config/dbtz.php', 'dbtz'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/dbtz.php' => config_path('dbtz.php'),
        ]);

        try {
            // Query DB timezone offset
            $offset = DB::select(DB::raw("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) AS `offset`"))[0]->offset;
            list($hrs, $mins, $secs) = explode(":", $offset);
        } catch (\Exception $ex) {
            echo "Warning: failed to retrieve database timezone. disabling timezone handling." . PHP_EOL;
            return;
        }

        Config::set("database.connections.".config("database.default").".timezone", "+{$hrs}:{$mins}");
        
        // Register all model observers
        $paths = config("dbtz.search_path.models");
	foreach($paths as $namespace => $dir) {
	    if (!\File::exists($dir)) 
                continue;	
            $filesInFolder = \File::files($dir);

            foreach($filesInFolder as $path) {
                $modelClassName = $namespace.ucfirst(basename($path, '.php'));
                if (!class_exists($modelClassName)) {
                    echo "Warning: $modelClassName is not a valid class. Skipping." . PHP_EOL;
                    continue;
                }

                if (!method_exists($modelClassName, "observe")) {
                    echo "Warning: $modelClassName is not an observable Model. Skipping." . PHP_EOL;
                    continue;
                }
                $modelClassName::observe(DateTimeObserver::class);
            }
        }
    }
}
