<?php 

use FinanceApp\Controller\UserController;
use FinanceApp\Repository\UserRepository;
use FinanceApp\Service\UserService;

// services
$app['users.controller'] = function ($app) {
    return new UserController($app['users.service']);
};

$app['users.service'] = function ($app) {
    return new UserService($app['users.repository']);
};

$app['users.repository'] = function ($app) {
    return new UserRepository($app['db']);
};