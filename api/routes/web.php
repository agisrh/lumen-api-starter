<?php
use \Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json([
         'Welocme to API',
         //$router->app->version()
    ]);
});


# Auth Route
$router->group(['prefix' => 'v1/auth/'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
});

$router->group(['prefix' => 'v1/auth', 'middleware' => 'jwt.auth'], function () use ($router){
    $router->post('/reset', 'AuthController@sendResetToken');
    $router->post('/reset/{token}', 'AuthController@verifyResetPassword');
    $router->post('/refresh', 'AuthController@refresh');
    $router->post('/logout', 'AuthController@logout');
    $router->get('/session', 'AuthController@user');
});

# User Route
$router->group(['prefix' => 'v1/user', 'middleware' => 'jwt.auth'], function () use ($router){
    $router->get('/activity', 'UserController@profile');
    $router->post('/token/fcm/refresh', 'UserController@updateFCMToken');
});


# Notification Route
$router->group(['prefix' => 'v1/notification'], function () use ($router){
    $router->post('/test', 'NotificationController@sendNotification');
});

# Contact Route
$router->group(['prefix' => 'v1'], function () use ($router){
    $router->get('/contacts', 'ContactController@index');
    $router->get('/contacts/{id}', 'ContactController@edit');
    $router->post('/contacts/create', 'ContactController@store');
    $router->post('/contacts/update/{id}', 'ContactController@update');
    $router->get('/contacts/delete/{id}', 'ContactController@destroy');
});