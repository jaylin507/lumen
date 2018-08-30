<?php

use Laravel\Lumen\Routing\Router;

/**
 * Api路由
 * @var Router $router
 */

$params = [
    'prefix' => 'api',
    'namespace' => 'Api\Controllers'
];
$router->group($params, function (Router $api) {

    $api->get('index', 'IndexController@index');


});
