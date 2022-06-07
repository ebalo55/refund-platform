<?php

use App\Http\Controllers\CollectorController;
use App\Http\Controllers\UpdaterController;
use App\Http\Controllers\Web3;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::permanentRedirect("login", "/");
Route::permanentRedirect("register", "/");

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/identify', [CollectorController::class, "renderCollectInformationPage"])->name('authenticated.get.collector.identify');
    Route::post('/identify', [CollectorController::class, "collectInformation"])->name('authenticated.post.collector.identify');

    Route::get('/dashboard', [UpdaterController::class, "displayDashboard"])->name('authenticated.get.updater.dashboard');
    Route::get('/refund-state', [UpdaterController::class, "isRefundCompleted"])->name('authenticated.get.updater.refund_completed');
    Route::post('/address', [UpdaterController::class, "updateRefundAddress"])->name('authenticated.post.updater.update_address');
});

Route::prefix("web3")->group(function() {
    Route::get("message", [Web3::class, "message"])->name("public.get.web3.message");
    Route::post("verify", [Web3::class, "verify"])->name("public.post.web3.verify");
});
