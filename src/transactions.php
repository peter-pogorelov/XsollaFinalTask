<?php
use FinanceApp\Controller\TransactionController;
use FinanceApp\Repository\TransactionRepository;
use FinanceApp\Service\TransactionService;

//transactions
$app['transactions.controller'] = function ($app) {
	return new TransactionController(
		$app['users.service'],
		$app['accounts.service'],
		$app['transactions.service'], 
		$app['categories.service']
	);
};

$app['transactions.service'] = function ($app) {
	return new TransactionService($app['transactions.repository']);
};

$app['transactions.repository'] = function ($app) {
	return new TransactionRepository($app['db'], $app['db.options']['dbname']);
};