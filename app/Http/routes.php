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

    $router->get('/shops', 'ShopController@getAllShops');                       //  +
    $router->get('/shops/popular/{count?}/{search?}', 'ShopController@getPopularShops');  //  +
    $router->get('/shop/{id}', 'ShopController@getShop');                       //  +

    $router->get('/categories/popular/{count?}/{search?}', 'CategoryController@getPopularCategories');    //  +
    $router->get('/category/{id}', 'CategoryController@getCategory');                           //  +

    $router->get('/brands/popular/{count?}/{search?}', 'BrandController@getBrands');   //  +
    $router->get('/brand/{id}', 'BrandController@getBrand');    //  +

    $router->get('/user/{id}', 'UserController@getUserById');               //  +
});

/**
 *  Admin Routes (Forbidden for not admin users)
 */
Route::group([
    'prefix' => 'v1/admin',
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

    $router->get('/report/{name}', 'ReportController@getReportData');

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
    $router->post('protected', 'AuthenticateController@isProtected');       //  +

    $router->get('/users', 'UserController@getAllUsers');                   //  +

    $router->delete('/profile', 'UserController@deleteProfile');            //  +
    $router->post('/profile', 'UserController@restoreProfile');             //  +
    $router->put('/profile', 'UserController@editProfile');

    $router->patch('/follow/{id}', 'FollowerController@follow');            //  +
    $router->delete('/follow/{id}', 'FollowerController@unFollow');         //  +
    $router->get('/followers', 'FollowerController@getMyFollowers');        //  +
    $router->get('/followers/{id}', 'FollowerController@getUserFollowers'); //  +
    $router->get('/followed', 'FollowerController@getMyFollowed');          //  +
    $router->get('/followed/{id}', 'FollowerController@getUserFollowed');   //  +

    $router->get('/shops/my/{count?}', 'ShopController@getMyShops');                 //  +
    $router->get('/brands/{search?}', 'BrandController@getBrands');         //  +
    $router->get('/products/category/{categoryIds}/{search?}', 'ProductController@getProductsByCategory');  //  +
    $router->get('/products/brand/{brandIds}/{search?}', 'ProductController@getProductsByBrand');           //  +

    $router->get('/product/{id}', 'ProductController@getProductById');

    $router->get('/basket', 'BasketController@getBasket');                                  //  +
    $router->post('/basket/{productId}/{count?}', 'BasketController@addProduct');           //  +
    $router->delete('/basket/{productId}', 'BasketController@removeProduct');                //  +
    $router->patch('/basket/{basketId}/{count}', 'BasketController@changeProductCount');   //  +

    $router->get('/orders', 'OrderController@getMyOrders');         //  +
    $router->post('/order', 'OrderController@makeOrder');

    $router->get('/discount/{search}', 'OrderController@searchDiscount');

});
