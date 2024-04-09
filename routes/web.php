<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ValueSERPController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ValueSERPController::class, 'index']);
Route::post('/search', [ValueSERPController::class, 'search']);
Route::get('/export', [ValueSERPController::class, 'export'])->name('export');

