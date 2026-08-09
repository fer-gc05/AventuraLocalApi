<?php

namespace App\Providers;

use App\Models\Destination;
use App\Models\Event;
use App\Models\Route;
use App\Models\Tour;
use App\Models\User;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CommunityRepositoryInterface;
use App\Repositories\Contracts\DestinationRepositoryInterface;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\GuideProfileRepositoryInterface;
use App\Repositories\Contracts\MediaRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\RouteRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TourRepositoryInterface;
use App\Repositories\Contracts\TourScheduleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Implementations\CategoryRepositoryImplement;
use App\Repositories\Implementations\CommunityRepositoryImplement;
use App\Repositories\Implementations\DestinationRepositoryImplement;
use App\Repositories\Implementations\EventRepositoryImplement;
use App\Repositories\Implementations\GuideProfileRepositoryImplement;
use App\Repositories\Implementations\MediaRepositoryImplement;
use App\Repositories\Implementations\MessageRepositoryImplement;
use App\Repositories\Implementations\ReservationRepositoryImplement;
use App\Repositories\Implementations\ReviewRepositoryImplement;
use App\Repositories\Implementations\RouteRepositoryImplement;
use App\Repositories\Implementations\TagRepositoryImplement;
use App\Repositories\Implementations\TourRepositoryImplement;
use App\Repositories\Implementations\TourScheduleRepositoryImplement;
use App\Repositories\Implementations\UserRepositoryImplement;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryImplement::class);
        $this->app->bind(TourRepositoryInterface::class, TourRepositoryImplement::class);
        $this->app->bind(TourScheduleRepositoryInterface::class, TourScheduleRepositoryImplement::class);
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepositoryImplement::class);
        $this->app->bind(DestinationRepositoryInterface::class, DestinationRepositoryImplement::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepositoryImplement::class);
        $this->app->bind(GuideProfileRepositoryInterface::class, GuideProfileRepositoryImplement::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepositoryImplement::class);
        $this->app->bind(RouteRepositoryInterface::class, RouteRepositoryImplement::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepositoryImplement::class);
        $this->app->bind(CommunityRepositoryInterface::class, CommunityRepositoryImplement::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepositoryImplement::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepositoryImplement::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepositoryImplement::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'tour' => Tour::class,
            'destination' => Destination::class,
            'route' => Route::class,
            'event' => Event::class,
        ]);

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
