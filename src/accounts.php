<?php 

use FinanceApp\Controller\AccountController;
use FinanceApp\Repository\AccountRepository;
use FinanceApp\Service\AccountService;

//accounts
$app['accounts.controller'] = function ($app) {
	return new AccountController($app['users.service'], $app['accounts.service']);
};

$app['accounts.service'] = function ($app) {
	return new AccountService($app['accounts.repository']);
};

$app['accounts.repository'] = function($app) {
	return new AccountRepository($app['db']);
};