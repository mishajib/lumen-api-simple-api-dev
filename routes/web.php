<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('/', 'ExampleController@index');

    // User Authentication
    $router->post('/login', 'UserController@authenticate');

    // User Resource
    $router->post('/users', 'UserController@create');

    // Restricted Routes
    $router->group(['middleware' => "auth:api"], function () use ($router) {
        $router->get('/users', 'UserController@index');
        $router->get('/me', 'UserController@me');
    });

});
