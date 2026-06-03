<?php

use App\Http\Controllers\Api\Admin\AttractionController as AdminAttractionController;
use App\Http\Controllers\Api\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Api\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Api\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Api\Admin\PackageItemController as AdminPackageItemController;
use App\Http\Controllers\Api\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Api\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\Admin\UploadController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FilterOptionController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelReservationController;
use App\Http\Controllers\Api\MapItemController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MyReservationController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\RestaurantReservationController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\TripItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'version' => 'mvp',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

Route::get('/matches', [MatchController::class, 'index']);
Route::get('/matches/{id}/nearby', [MatchController::class, 'nearby'])->whereNumber('id');
Route::get('/matches/{id}', [MatchController::class, 'show'])->whereNumber('id');

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show'])->whereNumber('id');

Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->whereNumber('id');

Route::get('/attractions', [AttractionController::class, 'index']);
Route::get('/attractions/{id}', [AttractionController::class, 'show'])->whereNumber('id');

Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{id}', [PackageController::class, 'show'])->whereNumber('id');

Route::get('/map-items', MapItemController::class);

Route::get('/filter-options', [FilterOptionController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/hotel-reservations', [HotelReservationController::class, 'store']);
    Route::post('/restaurant-reservations', [RestaurantReservationController::class, 'store']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->whereNumber('id');
    Route::get('/my-reservations', [MyReservationController::class, 'index']);
    Route::put('/my-hotel-reservations/{reservation}/cancel', [MyReservationController::class, 'cancelHotel'])->whereNumber('reservation');
    Route::put('/my-restaurant-reservations/{reservation}/cancel', [MyReservationController::class, 'cancelRestaurant'])->whereNumber('reservation');
    Route::post('/my-hotel-reservations/{reservation}/pay', [MyReservationController::class, 'payHotel'])->whereNumber('reservation');
    Route::post('/my-restaurant-reservations/{reservation}/pay', [MyReservationController::class, 'payRestaurant'])->whereNumber('reservation');
    Route::apiResource('trips', TripController::class);
    Route::post('/trips/{trip}/items', [TripItemController::class, 'store'])->whereNumber('trip');
    Route::put('/trips/{trip}/items/{item}', [TripItemController::class, 'update'])->whereNumber('trip')->whereNumber('item');
    Route::delete('/trips/{trip}/items/{item}', [TripItemController::class, 'destroy'])->whereNumber('trip')->whereNumber('item');
});

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {
        Route::get('/stats', StatsController::class);
        Route::post('/upload', UploadController::class);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->whereNumber('user');
        Route::get('/reservations', [AdminReservationController::class, 'index']);
        Route::put('/hotel-reservations/{reservation}/status', [AdminReservationController::class, 'updateHotelStatus'])->whereNumber('reservation');
        Route::put('/restaurant-reservations/{reservation}/status', [AdminReservationController::class, 'updateRestaurantStatus'])->whereNumber('reservation');

        Route::apiResource('matches', AdminMatchController::class);
        Route::apiResource('hotels', AdminHotelController::class);
        Route::apiResource('restaurants', AdminRestaurantController::class);
        Route::apiResource('attractions', AdminAttractionController::class);
        Route::apiResource('packages', AdminPackageController::class);
        Route::post('/packages/{package}/items', [AdminPackageItemController::class, 'store'])->whereNumber('package');
        Route::put('/packages/{package}/items/{item}', [AdminPackageItemController::class, 'update'])->whereNumber('package')->whereNumber('item');
        Route::delete('/packages/{package}/items/{item}', [AdminPackageItemController::class, 'destroy'])->whereNumber('package')->whereNumber('item');
        Route::put('/packages/{package}/items/{item}/move/{direction}', [AdminPackageItemController::class, 'move'])->whereNumber('package')->whereNumber('item')->whereIn('direction', ['up', 'down']);
    });
