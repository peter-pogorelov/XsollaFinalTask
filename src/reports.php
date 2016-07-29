<?php 

use FinanceApp\Controller\ReportController;
use FinanceApp\Repository\ReportRepository;
use FinanceApp\Service\ReportService;

//accounts
$app['reports.controller'] = function ($app) {
	return new ReportController(
		$app['users.service'], 
		$app['accounts.service'], 
		$app['reports.service'], 
		$app['categories.service']
	);
};

$app['reports.service'] = function ($app) {
	return new ReportService($app['reports.repository']);
};

$app['reports.repository'] = function($app) {
	return new ReportRepository($app['db'], $app['db.options']['dbname']);
};