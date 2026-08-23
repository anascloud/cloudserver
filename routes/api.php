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
use App\Http\Controllers\Projects\ProjectsController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\FM\CompanyController;
use App\Http\Controllers\FM\PaymentController;
use App\Http\Controllers\FM\BankAccountController;
use App\Http\Controllers\FM\DashboardController;
use App\Http\Controllers\SCM\FeatureReportController;
use App\Http\Controllers\CRM\CustomerController;
use App\Http\Controllers\CRM\AccountReceivableController;
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

    // Handle CORS preflight for all API routes
    Route::options('/{any}', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
    })->where('any', '.*');

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
        Route::resource('attribute', AttributeController::class);
        Route::get('attributes/view/all', [AttributeController::class, 'indexAll']);
        Route::get('attributes/view/search', [AttributeController::class, 'search']);
        Route::resource('customer', CustomerController::class);
        Route::get('reporting/account-recivable-report', [AccountReceivableController::class, 'index']);
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

    /**
     * Project Module
     */
    Route::resource('projects', ProjectsController::class);
    Route::post('projects/{id}', [ProjectsController::class, 'update']);
    Route::get('projects/view/all', [ProjectsController::class, 'indexAll']);
    Route::get('projects/view/search', [ProjectsController::class, 'search']);

    /**
     * System Module
     */
    Route::get('clear', [SystemController::class, 'clear']);
    Route::get('migrate', [SystemController::class, 'migrate']);
    Route::get('migrate-fresh', [SystemController::class, 'migrateFresh']);

    /**
     * FMS - Company Module
     */
    Route::prefix('fm/company')->group(function () {
        Route::get('get-all-company', [CompanyController::class, 'indexAll']);
        Route::get('{id}', [CompanyController::class, 'show']);
        Route::post('add-company', [CompanyController::class, 'store']);
        Route::post('update-company', [CompanyController::class, 'update']);
        Route::get('delete-company/{id}', [CompanyController::class, 'destroy']);
        Route::delete('delete-companies', [CompanyController::class, 'bulkDelete']);
    });

    /**
     * FMS - Payment Module
     */
    Route::prefix('fm/payment')->group(function () {
        Route::get('get-all-payments', [PaymentController::class, 'indexAll']);
        Route::get('{id}', [PaymentController::class, 'show']);
        Route::post('add-payment', [PaymentController::class, 'store']);
        Route::post('update-payment', [PaymentController::class, 'update']);
        Route::get('delete-payment/{id}', [PaymentController::class, 'destroy']);
        Route::delete('delete-payments', [PaymentController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Account Module
     */
    Route::prefix('fm/bankaccount')->group(function () {
        Route::get('get-all-bank-account', [BankAccountController::class, 'indexAll']);
        Route::get('{id}', [BankAccountController::class, 'show']);
        Route::post('add-bank-account', [BankAccountController::class, 'store']);
        Route::post('update-bank-account', [BankAccountController::class, 'update']);
        Route::get('delete-bank-account/{id}', [BankAccountController::class, 'destroy']);
        Route::delete('delete-bank-accounts', [BankAccountController::class, 'bulkDelete']);
    });

    /**
     * FMS - Dashboard Module
     */
    Route::prefix('fm/dashboard')->group(function () {
        Route::get('get-top-company-income-report', [DashboardController::class, 'getTopCompanyIncome']);
        Route::get('get-company-wise-profit-report', [DashboardController::class, 'getCompanyWiseProfit']);
        Route::get('top-bank-accounts-with-history', [DashboardController::class, 'getTopBankAccountsWithHistory']);
        Route::get('cash-flow', [DashboardController::class, 'getCashFlow']);
    });

    /**
     * SCM - Feature Reports Module
     */
    Route::prefix('scm/v1/feature-report')->group(function () {
        Route::get('accounts-payable-report', [FeatureReportController::class, 'getAccountPayableReport']);
    });

    /**
     * Fallback - catch all undefined API routes
     */
    Route::fallback(function () {
        return response()->json(['message' => 'Route Not Found', 'type' => 'error'], 404);
    });

});

