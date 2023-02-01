<?php

use Illuminate\Support\Facades\Route;

use App\Task\AmmoCrmSender;
use App\Task\CrmOrderSender;

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

Route::get('/', function () {
    return view('welcome');
});

	
// function sendOrderToCrm(CrmOrderSender $sender)
// {
//     return $sender->sendOrder([
//     	'order'
//     ]);
// }

// dd(
// 	sendOrderToCrm(
// 		new AmmoCrmSender('login', '123')
// 	)
// );