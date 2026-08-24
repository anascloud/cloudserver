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
use App\Http\Controllers\FM\CountryController;
use App\Http\Controllers\FM\PaymentController;
use App\Http\Controllers\FM\BankAccountController;
use App\Http\Controllers\FM\AccountingTypeController;
use App\Http\Controllers\FM\CurrencyController;
use App\Http\Controllers\FM\ZatcaCategoryController;
use App\Http\Controllers\FM\BankController;
use App\Http\Controllers\FM\BankAccountTypeController;
use App\Http\Controllers\FM\AssetLocationController;
use App\Http\Controllers\FM\FiscalYearController;
use App\Http\Controllers\FM\BudgetAgainstController;
use App\Http\Controllers\FM\JournalEntryTypeController;
use App\Http\Controllers\FM\TermsAndConditionsController;
use App\Http\Controllers\FM\AssetCategoryController;
use App\Http\Controllers\FM\CostCenterController;
use App\Http\Controllers\FM\TaxCategoryController;
use App\Http\Controllers\FM\ModeOfPaymentController;
use App\Http\Controllers\FM\ChartOfAccountController;
use App\Http\Controllers\FM\JournalEntryController;
use App\Http\Controllers\FM\JournalTemplateController;
use App\Http\Controllers\FM\CurrencyExchangeController;
use App\Http\Controllers\FM\BudgetDistributionController;
use App\Http\Controllers\FM\BudgetController;
use App\Http\Controllers\FM\AssetController;
use App\Http\Controllers\FM\AssetMovementController;
use App\Http\Controllers\FM\AssetMaintenanceController;
use App\Http\Controllers\FM\AssetRepairController;
use App\Http\Controllers\FM\AssetDepreciationController;
use App\Http\Controllers\FM\TaxRuleController;
use App\Http\Controllers\FM\TaxTemplateController;
use App\Http\Controllers\FM\PaymentRequestController;
use App\Http\Controllers\FM\BankTransactionController;
use App\Http\Controllers\FM\BankStatementController;
use App\Http\Controllers\FM\BankClearanceController;
use App\Http\Controllers\FM\BankReconciliationController;
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
    Route::put('users/{id}', [UsersController::class, 'update']);
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
     * FMS - Country Module
     */
    Route::prefix('fm/country')->group(function () {
        Route::get('get-all-country', [CountryController::class, 'indexAll']);
        Route::get('{id}', [CountryController::class, 'show']);
        Route::post('add-country', [CountryController::class, 'store']);
        Route::post('update-country', [CountryController::class, 'update']);
        Route::get('delete-country/{id}', [CountryController::class, 'destroy']);
        Route::delete('delete-countries', [CountryController::class, 'bulkDelete']);
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
     * FMS - Accounting Type Module
     */
    Route::prefix('fm/accounttype')->group(function () {
        Route::get('get-all-accounting-type', [AccountingTypeController::class, 'indexAll']);
        Route::get('{id}', [AccountingTypeController::class, 'show']);
        Route::post('add-accounting-type', [AccountingTypeController::class, 'store']);
        Route::post('update-accounting-type', [AccountingTypeController::class, 'update']);
        Route::get('delete-accounting-type/{id}', [AccountingTypeController::class, 'destroy']);
        Route::delete('delete-accounting-types', [AccountingTypeController::class, 'bulkDelete']);
    });

    /**
     * FMS - Currency Module
     */
    Route::prefix('fm/currency')->group(function () {
        Route::get('get-all-currency', [CurrencyController::class, 'indexAll']);
        Route::get('{id}', [CurrencyController::class, 'show']);
        Route::post('add-currency', [CurrencyController::class, 'store']);
        Route::post('update-currency', [CurrencyController::class, 'update']);
        Route::get('delete-currency/{id}', [CurrencyController::class, 'destroy']);
        Route::delete('delete-currencies', [CurrencyController::class, 'bulkDelete']);
    });

    /**
     * FMS - ZATCA Category Module
     */
    Route::prefix('fm/zatcacategory')->group(function () {
        Route::get('get-all-zatca-category', [ZatcaCategoryController::class, 'indexAll']);
        Route::get('{id}', [ZatcaCategoryController::class, 'show']);
        Route::post('add-zatca-category', [ZatcaCategoryController::class, 'store']);
        Route::post('update-zatca-category', [ZatcaCategoryController::class, 'update']);
        Route::get('delete-zatca-category/{id}', [ZatcaCategoryController::class, 'destroy']);
        Route::delete('delete-zatca-categories', [ZatcaCategoryController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Module
     */
    Route::prefix('fm/bank')->group(function () {
        Route::get('get-all-bank', [BankController::class, 'indexAll']);
        Route::get('{id}', [BankController::class, 'show']);
        Route::post('add-bank', [BankController::class, 'store']);
        Route::post('update-bank', [BankController::class, 'update']);
        Route::get('delete-bank/{id}', [BankController::class, 'destroy']);
        Route::delete('delete-banks', [BankController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Account Type Module
     */
    Route::prefix('fm/bankaccounttype')->group(function () {
        Route::get('get-all-bank-account-type', [BankAccountTypeController::class, 'indexAll']);
        Route::get('{id}', [BankAccountTypeController::class, 'show']);
        Route::post('add-bank-account-type', [BankAccountTypeController::class, 'store']);
        Route::post('update-bank-account-type', [BankAccountTypeController::class, 'update']);
        Route::get('delete-bank-account-type/{id}', [BankAccountTypeController::class, 'destroy']);
        Route::delete('delete-bank-account-types', [BankAccountTypeController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Location Module
     */
    Route::prefix('fm/assetlocation')->group(function () {
        Route::get('get-all-asset-location', [AssetLocationController::class, 'indexAll']);
        Route::get('{id}', [AssetLocationController::class, 'show']);
        Route::post('add-asset-location', [AssetLocationController::class, 'store']);
        Route::post('update-asset-location', [AssetLocationController::class, 'update']);
        Route::get('delete-asset-location/{id}', [AssetLocationController::class, 'destroy']);
        Route::delete('delete-asset-locations', [AssetLocationController::class, 'bulkDelete']);
    });

    /**
     * FMS - Fiscal Year Module
     */
    Route::prefix('fm/fiscalyear')->group(function () {
        Route::get('get-all-fiscal-year', [FiscalYearController::class, 'indexAll']);
        Route::get('{id}', [FiscalYearController::class, 'show']);
        Route::post('add-fiscal-year', [FiscalYearController::class, 'store']);
        Route::post('update-fiscal-year', [FiscalYearController::class, 'update']);
        Route::get('delete-fiscal-year/{id}', [FiscalYearController::class, 'destroy']);
        Route::delete('delete-fiscal-years', [FiscalYearController::class, 'bulkDelete']);
    });

    /**
     * FMS - Budget Against Module
     */
    Route::prefix('fm/budgetagainst')->group(function () {
        Route::get('get-all-budget-against', [BudgetAgainstController::class, 'indexAll']);
        Route::get('{id}', [BudgetAgainstController::class, 'show']);
        Route::post('add-budget-against', [BudgetAgainstController::class, 'store']);
        Route::post('update-budget-against', [BudgetAgainstController::class, 'update']);
        Route::get('delete-budget-against/{id}', [BudgetAgainstController::class, 'destroy']);
        Route::delete('delete-budget-againsts', [BudgetAgainstController::class, 'bulkDelete']);
    });

    /**
     * FMS - Journal Entry Type Module
     */
    Route::prefix('fm/journaltype')->group(function () {
        Route::get('get-all-journal-types', [JournalEntryTypeController::class, 'indexAll']);
        Route::get('{id}', [JournalEntryTypeController::class, 'show']);
        Route::post('add-journal-type', [JournalEntryTypeController::class, 'store']);
        Route::post('update-journal-type', [JournalEntryTypeController::class, 'update']);
        Route::get('delete-journal-type/{id}', [JournalEntryTypeController::class, 'destroy']);
        Route::delete('delete-journal-types', [JournalEntryTypeController::class, 'bulkDelete']);
    });

    /**
     * FMS - Terms and Conditions Module
     */
    Route::prefix('fm/compliancetermsandcondition')->group(function () {
        Route::get('get-all-compliance-terms-and-condition', [TermsAndConditionsController::class, 'indexAll']);
        Route::get('{id}', [TermsAndConditionsController::class, 'show']);
        Route::post('add-compliance-terms-and-condition', [TermsAndConditionsController::class, 'store']);
        Route::post('update-compliance-terms-and-condition', [TermsAndConditionsController::class, 'update']);
        Route::get('delete-compliance-terms-and-condition/{id}', [TermsAndConditionsController::class, 'destroy']);
        Route::delete('delete-compliance-terms-and-conditions', [TermsAndConditionsController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Category Module
     */
    Route::prefix('fm/assetcategory')->group(function () {
        Route::get('get-all-asset-category', [AssetCategoryController::class, 'indexAll']);
        Route::get('{id}', [AssetCategoryController::class, 'show']);
        Route::post('add-asset-category', [AssetCategoryController::class, 'store']);
        Route::post('update-asset-category', [AssetCategoryController::class, 'update']);
        Route::get('delete-asset-category/{id}', [AssetCategoryController::class, 'destroy']);
        Route::delete('delete-asset-categories', [AssetCategoryController::class, 'bulkDelete']);
    });

    /**
     * FMS - Cost Center Module
     */
    Route::prefix('fm/costcenter')->group(function () {
        Route::get('get-all-cost-center', [CostCenterController::class, 'indexAll']);
        Route::get('{id}', [CostCenterController::class, 'show']);
        Route::post('add-cost-center', [CostCenterController::class, 'store']);
        Route::post('update-cost-center', [CostCenterController::class, 'update']);
        Route::get('delete-cost-center/{id}', [CostCenterController::class, 'destroy']);
        Route::delete('delete-cost-centers', [CostCenterController::class, 'bulkDelete']);
    });

    /**
     * FMS - Tax Category Module
     */
    Route::prefix('fm/taxcategory')->group(function () {
        Route::get('get-all-tax-category', [TaxCategoryController::class, 'indexAll']);
        Route::get('{id}', [TaxCategoryController::class, 'show']);
        Route::post('add-tax-category', [TaxCategoryController::class, 'store']);
        Route::post('update-tax-category', [TaxCategoryController::class, 'update']);
        Route::get('delete-tax-category/{id}', [TaxCategoryController::class, 'destroy']);
        Route::delete('delete-tax-categories', [TaxCategoryController::class, 'bulkDelete']);
    });

    /**
     * FMS - Mode of Payment Module
     */
    Route::prefix('fm/modeofpayment')->group(function () {
        Route::get('get-all', [ModeOfPaymentController::class, 'indexAll']);
        Route::get('{id}', [ModeOfPaymentController::class, 'show']);
        Route::post('add-mode-of-payment', [ModeOfPaymentController::class, 'store']);
        Route::post('update-mode-of-payment', [ModeOfPaymentController::class, 'update']);
        Route::get('delete/{id}', [ModeOfPaymentController::class, 'destroy']);
        Route::delete('delete-multiple', [ModeOfPaymentController::class, 'bulkDelete']);
    });

    /**
     * FMS - Chart of Accounts Module
     */
    Route::prefix('fm/chartofaccount')->group(function () {
        Route::get('get-all-chart-of-accounts', [ChartOfAccountController::class, 'indexAll']);
        Route::get('{id}', [ChartOfAccountController::class, 'show']);
        Route::post('add-chart-of-account', [ChartOfAccountController::class, 'store']);
        Route::post('update-chart-of-account', [ChartOfAccountController::class, 'update']);
        Route::get('delete-chart-of-account/{id}', [ChartOfAccountController::class, 'destroy']);
        Route::delete('delete-chart-of-accounts', [ChartOfAccountController::class, 'bulkDelete']);
    });

    /**
     * FMS - Journal Entry Module
     */
    Route::prefix('fm/journal')->group(function () {
        Route::get('get-all-journals', [JournalEntryController::class, 'indexAll']);
        Route::get('{id}', [JournalEntryController::class, 'show']);
        Route::post('add-journal', [JournalEntryController::class, 'store']);
        Route::post('update-journal', [JournalEntryController::class, 'update']);
        Route::get('delete-journal/{id}', [JournalEntryController::class, 'destroy']);
        Route::delete('delete-journals', [JournalEntryController::class, 'bulkDelete']);
    });

    /**
     * FMS - Journal Template Module
     */
    Route::prefix('fm/journaltemplate')->group(function () {
        Route::get('get-all-journal-templates', [JournalTemplateController::class, 'indexAll']);
        Route::get('{id}', [JournalTemplateController::class, 'show']);
        Route::post('add-journal-template', [JournalTemplateController::class, 'store']);
        Route::post('update-journal-template', [JournalTemplateController::class, 'update']);
        Route::get('delete-journal-template/{id}', [JournalTemplateController::class, 'destroy']);
        Route::delete('delete-journal-templates', [JournalTemplateController::class, 'bulkDelete']);
    });

    /**
     * FMS - Currency Exchange Module
     */
    Route::prefix('fm/currencyexchange')->group(function () {
        Route::get('get-all', [CurrencyExchangeController::class, 'indexAll']);
        Route::get('{id}', [CurrencyExchangeController::class, 'show']);
        Route::post('add', [CurrencyExchangeController::class, 'store']);
        Route::post('update', [CurrencyExchangeController::class, 'update']);
        Route::get('delete/{id}', [CurrencyExchangeController::class, 'destroy']);
        Route::delete('delete-multiple', [CurrencyExchangeController::class, 'bulkDelete']);
    });

    /**
     * FMS - Budget Distribution Module
     */
    Route::prefix('fm/budgetdistribution')->group(function () {
        Route::get('get-all-budget-distribution', [BudgetDistributionController::class, 'indexAll']);
        Route::get('{id}', [BudgetDistributionController::class, 'show']);
        Route::post('add-budget-distribution', [BudgetDistributionController::class, 'store']);
        Route::post('update-budget-distribution', [BudgetDistributionController::class, 'update']);
        Route::get('delete-budget-distribution/{id}', [BudgetDistributionController::class, 'destroy']);
        Route::delete('delete-budget-distributions', [BudgetDistributionController::class, 'bulkDelete']);
    });

    /**
     * FMS - Budget Module
     */
    Route::prefix('fm/budget')->group(function () {
        Route::get('get-all-budget', [BudgetController::class, 'indexAll']);
        Route::get('{id}', [BudgetController::class, 'show']);
        Route::post('add-budget', [BudgetController::class, 'store']);
        Route::post('update-budget', [BudgetController::class, 'update']);
        Route::get('delete-budget/{id}', [BudgetController::class, 'destroy']);
        Route::delete('delete-budgets', [BudgetController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Module
     */
    Route::prefix('fm/asset')->group(function () {
        Route::get('get-all-asset', [AssetController::class, 'indexAll']);
        Route::get('{id}', [AssetController::class, 'show']);
        Route::post('add-asset', [AssetController::class, 'store']);
        Route::post('update-asset', [AssetController::class, 'update']);
        Route::get('delete-asset/{id}', [AssetController::class, 'destroy']);
        Route::delete('delete-assets', [AssetController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Movement Module
     */
    Route::prefix('fm/assetmovement')->group(function () {
        Route::get('get-all-asset-movement', [AssetMovementController::class, 'indexAll']);
        Route::get('{id}', [AssetMovementController::class, 'show']);
        Route::post('add-asset-movement', [AssetMovementController::class, 'store']);
        Route::post('update-asset-movement', [AssetMovementController::class, 'update']);
        Route::get('delete-asset-movement/{id}', [AssetMovementController::class, 'destroy']);
        Route::delete('delete-asset-movements', [AssetMovementController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Maintenance Module
     */
    Route::prefix('fm/assetmaintenance')->group(function () {
        Route::get('get-all-asset-maintenance', [AssetMaintenanceController::class, 'indexAll']);
        Route::get('{id}', [AssetMaintenanceController::class, 'show']);
        Route::post('add-asset-maintenance', [AssetMaintenanceController::class, 'store']);
        Route::post('update-asset-maintenance', [AssetMaintenanceController::class, 'update']);
        Route::get('delete-asset-maintenance/{id}', [AssetMaintenanceController::class, 'destroy']);
        Route::delete('delete-asset-maintenances', [AssetMaintenanceController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Repair Module
     */
    Route::prefix('fm/assetrepair')->group(function () {
        Route::get('get-all-asset-repair', [AssetRepairController::class, 'indexAll']);
        Route::get('{id}', [AssetRepairController::class, 'show']);
        Route::post('add-asset-repair', [AssetRepairController::class, 'store']);
        Route::post('update-asset-repair', [AssetRepairController::class, 'update']);
        Route::get('delete-asset-repair/{id}', [AssetRepairController::class, 'destroy']);
        Route::delete('delete-asset-repairs', [AssetRepairController::class, 'bulkDelete']);
    });

    /**
     * FMS - Asset Depreciation Module
     */
    Route::prefix('fm/assetdepreciation')->group(function () {
        Route::get('get-all-asset-depreciation', [AssetDepreciationController::class, 'indexAll']);
        Route::get('{id}', [AssetDepreciationController::class, 'show']);
        Route::get('delete-asset-depreciation/{id}', [AssetDepreciationController::class, 'destroy']);
        Route::delete('delete-asset-depreciations', [AssetDepreciationController::class, 'bulkDelete']);
    });

    /**
     * FMS - Tax Rule Module
     */
    Route::prefix('fm/taxrule')->group(function () {
        Route::get('get-all-tax-rule', [TaxRuleController::class, 'indexAll']);
        Route::get('{id}', [TaxRuleController::class, 'show']);
        Route::post('add-tax-rule', [TaxRuleController::class, 'store']);
        Route::post('update-tax-rule', [TaxRuleController::class, 'update']);
        Route::get('delete-tax-rule/{id}', [TaxRuleController::class, 'destroy']);
        Route::delete('delete-tax-rules', [TaxRuleController::class, 'bulkDelete']);
    });

    /**
     * FMS - Tax Template Module
     */
    Route::prefix('fm/taxtemplate')->group(function () {
        Route::get('get-all-tax-template', [TaxTemplateController::class, 'indexAll']);
        Route::get('{id}', [TaxTemplateController::class, 'show']);
        Route::post('add-tax-template', [TaxTemplateController::class, 'store']);
        Route::post('update-tax-template', [TaxTemplateController::class, 'update']);
        Route::get('delete-tax-template/{id}', [TaxTemplateController::class, 'destroy']);
        Route::delete('delete-tax-templates', [TaxTemplateController::class, 'bulkDelete']);
    });

    /**
     * FMS - Payment Request Module
     */
    Route::prefix('fm/paymentrequest')->group(function () {
        Route::get('get-all', [PaymentRequestController::class, 'indexAll']);
        Route::get('{id}', [PaymentRequestController::class, 'show']);
        Route::post('add-payment-request', [PaymentRequestController::class, 'store']);
        Route::post('update-payment-request', [PaymentRequestController::class, 'update']);
        Route::get('delete/{id}', [PaymentRequestController::class, 'destroy']);
        Route::delete('delete-multiple', [PaymentRequestController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Transaction Module
     */
    Route::prefix('fm/banktransaction')->group(function () {
        Route::get('get-all-bank-transaction', [BankTransactionController::class, 'indexAll']);
        Route::get('{id}', [BankTransactionController::class, 'show']);
        Route::post('add-bank-transaction', [BankTransactionController::class, 'store']);
        Route::post('update-bank-transaction', [BankTransactionController::class, 'update']);
        Route::get('delete-bank-transaction/{id}', [BankTransactionController::class, 'destroy']);
        Route::delete('delete-bank-transactions', [BankTransactionController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Statement Module
     */
    Route::prefix('fm/bankstatement')->group(function () {
        Route::get('get-all-bank-statement', [BankStatementController::class, 'indexAll']);
        Route::get('{id}', [BankStatementController::class, 'show']);
        Route::post('add-bank-statement', [BankStatementController::class, 'store']);
        Route::post('update-bank-statement', [BankStatementController::class, 'update']);
        Route::get('delete-bank-statement/{id}', [BankStatementController::class, 'destroy']);
        Route::delete('delete-bank-statements', [BankStatementController::class, 'bulkDelete']);
    });

    /**
     * FMS - Bank Clearance Module
     */
    Route::prefix('fm/bankclearance')->group(function () {
        Route::get('get-all-bank-clearance', [BankClearanceController::class, 'indexAll']);
        Route::post('update-clearence-status', [BankClearanceController::class, 'updateClearanceStatus']);
        Route::post('update-clearance-status-batch', [BankClearanceController::class, 'updateClearanceStatusBatch']);
    });

    /**
     * FMS - Bank Reconciliation Module
     */
    Route::prefix('fm/bankreconciliation')->group(function () {
        Route::get('get-all-unreconciled-transaction', [BankReconciliationController::class, 'getUnreconciledTransactions']);
        Route::post('update-unreconciled-transaction/{id}', [BankReconciliationController::class, 'updateUnreconciledTransaction']);
        Route::get('get-all-unreconciled-payment', [BankReconciliationController::class, 'getUnreconciledPayments']);
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

