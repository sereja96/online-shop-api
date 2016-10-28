<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Public (Guest Routes)
 */
Route::group([
    'prefix' => 'v1',
    'middleware' => [
        'cors',
        'locale'
    ]
], function(\Illuminate\Routing\Router $router){
    $router->post('login', 'AuthenticateController@authenticate');
    $router->post('logout', 'AuthenticateController@logout');
    $router->get('profile', 'UserController@getProfile');
    $router->get('roles', 'UserController@getRoles');
    $router->put('/password/{email}', 'UserController@restorePassword');
    $router->get('/public/wishes/{link}', 'PublicWishLinkController@getWishes');        //  +
    $router->get('/cities/{search?}', 'CityController@searchCities');        //  +
    $router->patch('/profile', 'UserController@registration');

});

/**
 *  Admin Routes (Forbidden for not admin users)
 */
Route::group([
    'prefix' => 'admin',
    'middleware' => [
        'cors',
        'jwt.auth',
        'locale',
        'admin'
    ]
], function(\Illuminate\Routing\Router $router) {

    $router->get('/test', function () {
        return "success test!!";
    });

});

/**
 * Auth User
 */
Route::group([
    'prefix' => 'v1',
    'middleware' => [
        'cors',
        'jwt.auth',
        'locale'
    ]
], function(\Illuminate\Routing\Router $router) {
    $router->get('/user/{id}', 'UserController@getUser');                   //  +
    $router->get('/users', 'UserController@getAllUsers');                   //  +
    $router->get('/users/search/{search}', 'UserController@searchUsers');
    $router->post('/users/vk', 'UserController@searchVkUsers');
    $router->delete('/profile', 'UserController@deleteProfile');            //  +
    $router->post('/profile', 'UserController@restoreProfile');             //  +
    $router->put('/profile', 'UserController@editProfile');
    $router->post('/password', 'UserController@changePassword');            //  +

    $router->patch('/follow/{id}', 'FollowerController@follow');            //  +
    $router->post('/follow/{id}', 'FollowerController@decline');            //  +
    $router->put('/follow/{id}', 'FollowerController@confirm');             //  +
    $router->delete('/follow/{id}', 'FollowerController@unFollow');         //  +
    $router->get('/followers', 'FollowerController@getMyFollowers');        //  +
    $router->get('/followers/{id}', 'FollowerController@getUserFollowers'); //  +
    $router->get('/followed', 'FollowerController@getMyFollowed');          //  +
    $router->get('/followed/{id}', 'FollowerController@getUserFollowed');   //  +

    $router->get('/countries/{search?}', 'CityController@searchCountries');  //  +
});
