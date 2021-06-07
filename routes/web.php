<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', [VideoController::class, 'create']);
Route::post('/store', [VideoController::class, 'store']);
Route::post('/transcode', [VideoController::class, 'transcode']);
Route::post('/delete', [VideoController::class, 'delete']);
Route::get('/progress', [VideoController::class, 'getProgess']);
// Route::get('/{video}', [VideoController::class, 'show']);
