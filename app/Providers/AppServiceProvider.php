<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
        ->withDocumentTransformers(function(OpenApi $openApi){
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });

        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
    }
}
