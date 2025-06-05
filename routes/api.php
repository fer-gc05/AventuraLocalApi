<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;



Route::prefix('destinations')->group(function () {
    Route::get('/popular', [DestinationController::class, 'getPopularDestinations']);
    Route::get('/', [DestinationController::class, 'index']);
    Route::get('/{destination}', [DestinationController::class, 'show']);
    Route::get('/{destination}/reviews', [DestinationController::class, 'getDestinationReviews']);
    Route::get('/{destination}/routes', [DestinationController::class, 'getDestinationRoutes']);
    Route::get('/{destination}/events', [DestinationController::class, 'getDestinationEvents']);
});

Route::prefix('events')->group(function () {
    Route::get('/popular', [EventController::class, 'getPopularEvents']);
    Route::get('/upcoming', [EventController::class, 'getUpcomingEvents']);
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{event}', [EventController::class, 'show']);
});

Route::prefix('communities')->group(function () {
    Route::get('/popular', [CommunityController::class, 'getPopularCommunities']);
    Route::get('/', [CommunityController::class, 'index']);
    Route::get('/{community}', [CommunityController::class, 'show']);
});

Route::prefix('routes')->group(function () {
    Route::get('/popular', [RouteController::class, 'getPopularRoutes']);
    Route::get('/', [RouteController::class, 'index']);
    Route::get('/{route}', [RouteController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::get('/{category}/destinations', [CategoryController::class, 'destinations']);
});

Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index']);
    Route::get('/{tag}', [TagController::class, 'show']);
});


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});


Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/trashed', [UserController::class, 'trashed']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/restore', [UserController::class, 'restore']);
        Route::post('/profile-photo', [UserController::class, 'updateProfilePhoto']);

        Route::get('/{user}/communities', [UserController::class, 'getCommunities']);
        Route::get('/{user}/event-history', [UserController::class, 'getEventHistory']);
        Route::get('/{user}/favorite-routes', [UserController::class, 'getFavoriteRoutes']);
        Route::get('/{user}/favorite-destinations', [UserController::class, 'getFavoriteDestinations']);
        Route::get('/{user}/reviews', [UserController::class, 'getReviews']);
        Route::get('/{user}/statistics', [UserController::class, 'getStatistics']);

        Route::post('/destinations/{destination}/toggle-favorite', [UserController::class, 'toggleFavoriteDestination']);
        Route::post('/routes/{route}/toggle-favorite', [UserController::class, 'toggleFavoriteRoute']);
        Route::post('/routes/{route}/update-status', [UserController::class, 'updateRouteStatus']);
    });

    Route::prefix('tags')->group(function () {
        Route::post('/', [TagController::class, 'store']);
        Route::get('/trashed', [TagController::class, 'trashed']);
        Route::post('/{tag}/restore', [TagController::class, 'restore']);
        Route::put('/{tag}', [TagController::class, 'update']);
        Route::delete('/{tag}', [TagController::class, 'destroy']);
    });

    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/trashed', [CategoryController::class, 'trashed']);
        Route::post('/{category}/restore', [CategoryController::class, 'restore']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('destinations')->group(function () {
        Route::post('/', [DestinationController::class, 'store']);
        Route::get('/trashed', [DestinationController::class, 'trashed']);
        Route::get('/nearby', [DestinationController::class, 'nearby']);
        Route::get('/events', [DestinationController::class, 'events']);
        Route::post('/{destination}/restore', [DestinationController::class, 'restore']);
        Route::get('/{destination}/statistics', [DestinationController::class, 'getDestinationStatistics']);
        Route::put('/{destination}', [DestinationController::class, 'update']);
        Route::delete('/{destination}', [DestinationController::class, 'destroy']);
    });

    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::get('/trashed', [EventController::class, 'trashed']);
        Route::get('/nearby', [EventController::class, 'nearby']);
        Route::get('/recommendations', [EventController::class, 'getEventRecommendations']);
        Route::get('/calendar', [EventController::class, 'getEventCalendar']);
        Route::post('/{event}/restore', [EventController::class, 'restore']);
        Route::post('/{event}/attend', [EventController::class, 'attend']);
        Route::post('/{event}/cancel-attendance', [EventController::class, 'cancelAttendance']);
        Route::get('/{event}/attendees', [EventController::class, 'getAttendees']);
        Route::get('/{event}/statistics', [EventController::class, 'getEventStatistics']);
        Route::put('/{event}', [EventController::class, 'update']);
        Route::delete('/{event}', [EventController::class, 'destroy']);
    });

    Route::prefix('communities')->group(function () {
        Route::post('/', [CommunityController::class, 'store']);
        Route::get('/trashed', [CommunityController::class, 'trashed']);
        Route::get('/recommendations', [CommunityController::class, 'getCommunityRecommendations']);
        Route::post('/{id}/restore', [CommunityController::class, 'restore']);
        Route::post('/{community}/join', [CommunityController::class, 'join']);
        Route::get('/{community}/events', [CommunityController::class, 'getCommunityEvents']);
        Route::get('/{community}/routes', [CommunityController::class, 'getCommunityRoutes']);
        Route::put('/{community}', [CommunityController::class, 'update']);
        Route::delete('/{community}', [CommunityController::class, 'destroy']);
    });

    Route::prefix('routes')->group(function () {
        Route::post('/', [RouteController::class, 'store']);
        Route::get('/trashed', [RouteController::class, 'trashed']);
        Route::post('/{route}/restore', [RouteController::class, 'restore']);
        Route::post('/{route}/communities/attach', [RouteController::class, 'attachCommunity']);
        Route::post('/{route}/communities/detach', [RouteController::class, 'detachCommunity']);
        Route::post('/{route}/events/attach', [RouteController::class, 'attachEvent']);
        Route::post('/{route}/events/detach', [RouteController::class, 'detachEvent']);
        Route::get('/{route}/reviews', [RouteController::class, 'getRouteReviews']);
        Route::get('/{route}/communities', [RouteController::class, 'getRouteCommunities']);
        Route::get('/{route}/events', [RouteController::class, 'getRouteEvents']);
        Route::put('/{route}', [RouteController::class, 'update']);
        Route::delete('/{route}', [RouteController::class, 'destroy']);
    });

    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::post('/{review}/restore', [ReviewController::class, 'restore']);
        Route::get('/{review}', [ReviewController::class, 'show']);
        Route::put('/{review}', [ReviewController::class, 'update']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
    });

    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index']);
        Route::post('/', [ReservationController::class, 'store']);
        Route::get('/trashed', [ReservationController::class, 'trashed']);
        Route::post('/{reservation}/restore', [ReservationController::class, 'restore']);
        Route::get('/{reservation}', [ReservationController::class, 'show']);
        Route::put('/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/{reservation}', [ReservationController::class, 'destroy']);
    });

    Route::prefix('tours')->group(function () {
        Route::get('/', [TourController::class, 'index']);
        Route::post('/', [TourController::class, 'store']);
        Route::get('/trashed', [TourController::class, 'trashed']);
        Route::post('/{tour}/restore', [TourController::class, 'restore']);
        Route::get('/{tour}', [TourController::class, 'show']);
        Route::put('/{tour}', [TourController::class, 'update']);
        Route::delete('/{tour}', [TourController::class, 'destroy']);
    });
});