<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Repairorder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
        https://github.com/JosephSilber/bouncer
        All queries executed by the bouncer are cached for the current request. For better performance, you may want to use cross-request caching. To enable cross-request caching, add this to your AppServiceProvider's boot method:
        
        https://github.com/JosephSilber/bouncer#refreshing-the-cache
         */
        //Bouncer::cache();

        /*
        ako se nalog briše, neka se automatski postavi STATUS = DELETED
        //https://laravel.com/docs/5.1/eloquent#events
         */
        Repairorder::deleted(function($slucaj)
        {

            // SPREMI STATUS ("IZBRISAN")
            $slucaj->repairstatuses()->attach(\App\Http\Controllers\RepairorderController::getStatusDeleted(), array('loggeduser_id'=>\Auth::user()->id, 'user_id'=>\Auth::user()->id,'locationspp_id'=>\Auth::user()->location));
            return true; //if false the model wont save! 
        });
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
