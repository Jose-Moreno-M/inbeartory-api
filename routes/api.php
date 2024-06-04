<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ItemImageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ItemsEliminadosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Route::controller(AuthController::class)->group(function () {
    //     Route::post('login', 'login');
    //     Route::post('register', 'register');
    //     Route::post('logout', 'logout');
    //     Route::post('refresh', 'refresh');

    // });

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Routes to items:
    Route::get('/items', [ItemController::class, 'getItems']);
    Route::post('/user/items', [ItemController::class, 'getItemsByUsers']);
    Route::get('/items/{description}', [ItemController::class, 'getItemsBySearch']);

    Route::get('/items_filter', [ItemController::class, 'getItemsByColumn']);
    Route::post('/items', [ItemController::class, 'createItem']);
    Route::put('/items/{id}', [ItemController::class, 'editItem']);
    Route::post('/edit_image_item/{id}', [ItemController::class, 'editImage']);
    Route::delete('/items', [ItemController::class, 'deleteItem']);
    //Routes to exports
    Route::get('/report/excel', [ItemController::class, 'generateReportExcel']);
    Route::get('/report/pdf', [ItemController::class, 'generateReportPdf']);
    //Routes to images
    Route::get('/images/{filename}', [ItemImageController::class, 'show']);

    // Routes to deleted items
    Route::get('/deleted_items', [ItemsEliminadosController::class, 'getDeletedItems']);
    Route::get('/deleted_items_filter', [ItemsEliminadosController::class, 'getDeletedItemsByColumn']);
    Route::get('/user/deleted_items/{user_id}', [ItemsEliminadosController::class, 'getDeletedItems']);


    Route::get('/areas', [AreaController::class, 'getAreas']);
    Route::get('/user/areas/{user_id}', [AreaController::class, 'getUserAreas']);

    Route::post('/areas', [AreaController::class, 'createArea']);
    Route::put('/areas/{id}', [AreaController::class, 'editArea']);
    Route::delete('/areas/{id}', [AreaController::class, 'deleteArea']);
    Route::delete('/areas/{delete}/{transfer}', [AreaController::class, 'deleteAreaWithTransfer']);


    Route::get('/document/inventory/excel/{user_id}/{area_id}', [DocumentController::class, 'generateExcelInventoryReport']);
    Route::get('/document/inventory/pdf/{user_id}/{area_id}', [DocumentController::class, 'generatePdfInventoryReport']);
    Route::get('/document/casualties/pdf/{user_id}/{area_id}/{motive}', [DocumentController::class, 'generatePdfCasualtiesReport']);
    Route::post('/document/header', [DocumentController::class, 'uploadHeader']);
    Route::post('/document/footer', [DocumentController::class, 'uploadFooter']);
    Route::post('/document/header_and_footer', [DocumentController::class, 'uploadHeaderAndFooter']);

    // Routes to users
    Route::get('/auth/user', [AuthController::class, 'user'])->middleware('auth:api');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register/user', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::put('/users/{user_id}',[UserController::class, 'editUser']);
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::delete('/users/{user_id}',[UserController::class, 'deleteUser']);
    // Routes to roles
    Route::get('/roles', [RolesController::class, 'getRoles']);
});
