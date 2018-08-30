<?php

use Laravel\Lumen\Routing\Router;

/**
 * 管理后台系统路由
 * @var Router $router
 */

$params = [
    'prefix' => 'admin',
    'namespace' => 'Admin\Controllers',
    'middleware' => ['AuthFilter:admin', 'AdminAuth']
];
$router->group($params, function (Router $api) {

});
