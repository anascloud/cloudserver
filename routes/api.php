<?php
//app\Http\Controllers\Brands\BrandController.php
use App\Http\Controllers\Attributes\AttributeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\Permissions\PermissionsController;
use App\Http\Controllers\Feedbacks\FeedbacksController;
use App\Http\Controllers\Stocks\StocksController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\Campaigns\CampaignController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Units\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::options('/{any}', function () {
//     return response()->json(['status' => 'success']);
// })->where('any', '.*');

Route::group([
    'middleware' => 'api'
], function ($router) {

    /**
     * Authentication Module
     */
    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });


    /**
     * Users Module
     */
    Route::resource('users', UsersController::class);
    Route::post('users/{id}', [UsersController::class, 'update']);
    Route::get('users/view/all', [UsersController::class, 'indexAll']);
    Route::get('users/view/search', [UsersController::class, 'search']);
    Route::post('profile-reset-password', [AuthController::class, 'profileResetPassword']);

    /**
     * Roles Module
     */
    Route::resource('roles', RolesController::class);
    Route::get('roles/view/all', [RolesController::class, 'indexAll']);
    Route::get('roles/view/search', [RolesController::class, 'search']);

    /**
     * Permissions Module
     */
    Route::resource('permissions', PermissionsController::class);
    Route::get('permissions/view/all', [PermissionsController::class, 'indexAll']);
    Route::get('permissions/view/search', [PermissionsController::class, 'search']);

    /**
     * Category Module
     */
    Route::prefix('crm/v1')->group(function () {
        Route::resource('category', CategoryController::class);
        Route::get('categories/view/all', [CategoryController::class, 'indexAll']);
        Route::get('categories/view/search', [CategoryController::class, 'search']);
    });

    /**
     * Brand Module
     */
    Route::resource('brands', BrandController::class);
    Route::get('brands/view/all', [BrandController::class, 'indexAll']);
    Route::get('brands/view/search', [BrandController::class, 'search']);

    /**
     * unit Module
     */
    Route::resource('units', UnitController::class);
    Route::get('units/view/all', [UnitController::class, 'indexAll']);
    Route::get('units/view/search', [UnitController::class, 'search']);

    /**
     * Attribute Module
     */
    Route::resource('attributes', AttributeController::class);
    Route::get('attributes/view/all', [AttributeController::class, 'indexAll']);
    Route::get('attributes/view/search', [AttributeController::class, 'search']);

    /**
     * Stock Module
     */
    Route::resource('stocks', StocksController::class);
    Route::get('stocks/view/all', [StocksController::class, 'indexAll']);
    Route::get('stocks/view/search', [StocksController::class, 'search']);


    /**
     * Feedbacks Module
     */
    // Route::resource('feedbacks', FeedbacksController::class);
    // Route::get('feedbacks/view/all', [FeedbacksController::class, 'indexAll']);
    // Route::get('feedbacks/view/search', [FeedbacksController::class, 'search']);


    /**
     * Products Module
     */
    Route::resource('products', ProductsController::class);
    Route::post('products/{id}', [ProductsController::class, 'update']);
    Route::get('products/view/all', [ProductsController::class, 'indexAll']);
    Route::get('products/view/search', [ProductsController::class, 'search']);


    /**
     * Campaign Module
     */
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{id}', [CampaignController::class, 'update']);
    Route::get('campaigns/view/all', [CampaignController::class, 'indexAll']);
    Route::get('campaigns/view/search', [CampaignController::class, 'search']);

    /**
     * Lead Module
     */
    Route::resource('leads', LeadController::class);
    Route::post('leads/{id}', [LeadController::class, 'update']);
    Route::get('leads/view/all', [LeadController::class, 'indexAll']);
    Route::get('leads/view/search', [LeadController::class, 'search']);

});

