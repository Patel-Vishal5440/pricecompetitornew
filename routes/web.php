<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OdooController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComponentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//Routes
Route::get('/', [LoginController::class, 'loginForm']);
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'dashboards'], function () {
        Route::get('/social-media', [DashboardController::class, 'index'])->name('dashboards.index');
        Route::get('/business', [DashboardController::class, 'business'])->name('dashboards.business');
        Route::get('/performance', [DashboardController::class, 'performance'])->name('dashboards.performance');
        Route::get('/ecommerce', [DashboardController::class, 'ecommerce'])->name('dashboards.ecommerce');
        Route::get('/crm', [DashboardController::class, 'crm'])->name('dashboards.crm');
        Route::get('/sales', [DashboardController::class, 'sales'])->name('dashboards.sales');
    });
  
    Route::group(['prefix' => 'pages'], function () {
        Route::get('/profile-setting', [ProfileController::class, 'profileSetting'])->name('pages.profileSetting');
    });
    
    // Profile routes
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/edit', [ProfileController::class, 'profileSetting'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/image/update', [ProfileController::class, 'updateImage'])
    ->name('profile.image.update');
    });

    // Role Management Routes
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::patch('/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    });

    // Permission Management Routes
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        Route::patch('/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');
    });

    // User Management Routes
    Route::group(['prefix' => 'user-management'], function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('user-management.create');
        Route::post('/', [UserManagementController::class, 'store'])->name('user-management.store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('user-management.show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('user-management.edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');
        Route::get('/{user}/permission', [UserManagementController::class, 'permissions'])->name('user-management.permissions');
    });

    Route::group(['prefix' => 'competitor'], function () {
        Route::get('/list', [CompetitorController::class, 'index'])->name('competitor.list');
        Route::get('/new', [CompetitorController::class, 'create'])->name('competitor.create');
        Route::post('/store', [CompetitorController::class, 'store'])->name('competitor.store');
        Route::get('/edit/{id}', [CompetitorController::class, 'edit'])->name('competitor.edit');
        Route::put('/update/{id}', [CompetitorController::class, 'update'])->name('competitor.update');
        Route::delete('/delete/{id}', [CompetitorController::class, 'destroy'])->name('competitor.destroy');
        
        // Test route for debugging
        Route::get('/test-delete/{id}', [CompetitorController::class, 'testDelete'])->name('competitor.test-delete');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/list', [CategoryController::class, 'getCategories'])->name('categories.list');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    Route::group(['prefix' => 'price-history'], function () {
        Route::get('/list', [App\Http\Controllers\PriceHistoryController::class, 'index'])->name('price_history.list');
    });

    Route::group(['prefix' => 'odoo'], function () {
        Route::get('/authenticate', [OdooController::class, 'authenticate'])->name('odoo.authenticate');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/list', [ProductController::class, 'index'])->name('products.list');
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::post('/add-link', [ProductController::class, 'addLink'])
        ->name('products.addLink');
        Route::post('/update-price', [ProductController::class, 'updatePrice'])
            ->name('products.updatePrice');
        Route::post('/update', [ProductController::class, 'update'])->name('products.update');
        Route::get('/get-competitor-urls', [ProductController::class, 'getCompetitorUrls'])->name('products.getCompetitorUrls');
        Route::get('/sync-specific', [ProductController::class, 'syncSpecificProduct'])->name('products.sync-specific');
        Route::post('/bulk-sync-pricing', [ProductController::class, 'bulkSyncPricing'])->name('products.bulkSyncPricing');
        Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');
        // Route::get('/sync-products', [ProductController::class, 'syncProducts']);
        Route::get('/sync-products', [ProductController::class, 'syncProducts'])->name('products.syncProducts');
        Route::post('/import-price-update', [ProductController::class, 'importPriceUpdate'])->name('products.importPriceUpdate');
        Route::post('/import-bulk-products', [ProductController::class, 'importBulkProducts'])->name('products.importBulkProducts');
        Route::get('/download-price-update-sample', [ProductController::class, 'downloadPriceUpdateSample'])->name('products.downloadPriceUpdateSample');
        Route::get('/download-bulk-products-sample', [ProductController::class, 'downloadBulkProductsSample'])->name('products.downloadBulkProductsSample');
    });

    Route::group(['prefix' => 'cron-jobs'], function () {
        Route::get('/', [CronJobController::class, 'index'])->name('cron-jobs.index');
        Route::patch('/{cronJob}/toggle-status', [CronJobController::class, 'toggleStatus'])->name('cron-jobs.toggle-status');
        Route::post('/{cronJob}/update-schedule', [CronJobController::class, 'updateSchedule'])->name('cron-jobs.update-schedule');
    });

    // Component Routes
    Route::group(['prefix' => 'components'], function () {
        Route::get('/', [ComponentController::class, 'index'])->name('components.index');
        Route::get('/avatar', [ComponentController::class, 'avatar'])->name('components.avatar');
        Route::get('/badge', [ComponentController::class, 'badge'])->name('components.badge');
        Route::get('/breadcrumbs', [ComponentController::class, 'breadcrumbs'])->name('components.breadcrumbs');
        Route::get('/buttons', [ComponentController::class, 'buttons'])->name('components.buttons');
        Route::get('/cards', [ComponentController::class, 'cards'])->name('components.cards');
        Route::get('/carousel', [ComponentController::class, 'carousel'])->name('components.carousel');
        Route::get('/checkbox', [ComponentController::class, 'checkbox'])->name('components.checkbox');
        Route::get('/collapse', [ComponentController::class, 'collapse'])->name('components.collapse');
        Route::get('/comments', [ComponentController::class, 'comments'])->name('components.comments');
        Route::get('/dashboard-base', [ComponentController::class, 'dashboardBase'])->name('components.dashboard-base');
        Route::get('/date-picker', [ComponentController::class, 'datePicker'])->name('components.date-picker');
        Route::get('/drawer', [ComponentController::class, 'drawer'])->name('components.drawer');
        Route::get('/drag-drop', [ComponentController::class, 'dragDrop'])->name('components.drag-drop');
        Route::get('/dropdown', [ComponentController::class, 'dropdown'])->name('components.dropdown');
        Route::get('/empty', [ComponentController::class, 'empty'])->name('components.empty');
        Route::get('/grid', [ComponentController::class, 'grid'])->name('components.grid');
        Route::get('/input', [ComponentController::class, 'input'])->name('components.input');
        Route::get('/list', [ComponentController::class, 'list'])->name('components.list');
        Route::get('/menu', [ComponentController::class, 'menu'])->name('components.menu');
        Route::get('/message', [ComponentController::class, 'message'])->name('components.message');
        Route::get('/modal', [ComponentController::class, 'modal'])->name('components.modal');
        Route::get('/notifications', [ComponentController::class, 'notifications'])->name('components.notifications');
        Route::get('/page-header', [ComponentController::class, 'pageHeader'])->name('components.page-header');
        Route::get('/pagination', [ComponentController::class, 'pagination'])->name('components.pagination');
        Route::get('/progressbar', [ComponentController::class, 'progressbar'])->name('components.progressbar');
        Route::get('/radio', [ComponentController::class, 'radio'])->name('components.radio');
        Route::get('/rate', [ComponentController::class, 'rate'])->name('components.rate');
        Route::get('/result', [ComponentController::class, 'result'])->name('components.result');
        Route::get('/select', [ComponentController::class, 'select'])->name('components.select');
        Route::get('/skeleton', [ComponentController::class, 'skeleton'])->name('components.skeleton');
        Route::get('/slider', [ComponentController::class, 'slider'])->name('components.slider');
        Route::get('/spin', [ComponentController::class, 'spin'])->name('components.spin');
        Route::get('/statistics', [ComponentController::class, 'statistics'])->name('components.statistics');
        Route::get('/steps', [ComponentController::class, 'steps'])->name('components.steps');
        Route::get('/switch', [ComponentController::class, 'switch'])->name('components.switch');
        Route::get('/tab', [ComponentController::class, 'tab'])->name('components.tab');
        Route::get('/tag', [ComponentController::class, 'tag'])->name('components.tag');
        Route::get('/timeline', [ComponentController::class, 'timeline'])->name('components.timeline');
        Route::get('/timeline2', [ComponentController::class, 'timelineTwo'])->name('components.timeline2');
        Route::get('/timeline3', [ComponentController::class, 'timelineThree'])->name('components.timeline3');
        Route::get('/time-picker', [ComponentController::class, 'timePicker'])->name('components.time-picker');
        Route::get('/uploads', [ComponentController::class, 'uploads'])->name('components.uploads');
    });

});
Auth::routes();