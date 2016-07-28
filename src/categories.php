<?php 

use FinanceApp\Controller\CategoryController;
use FinanceApp\Repository\CategoryRepository;
use FinanceApp\Service\CategoryService;


//categories
$app['categories.controller'] = function ($app) {
    return new CategoryController($app['categories.service']);
};

$app['categories.service'] = function ($app) {
	return new CategoryService($app['categories.repository']);
};

$app['categories.repository'] = function($app) {
	return new CategoryRepository($app['db'], $app['db.options']['dbname']);
};