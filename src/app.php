<?php
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../src/users.php';
require __DIR__ . '/../src/accounts.php';
require __DIR__ . '/../src/categories.php';
require __DIR__ . '/../src/transactions.php';
require __DIR__ . '/../src/reports.php';

$app->register(new DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'host' => '192.168.100.123',
        'dbname' => 'finance',
        'user' => 'root',
        'password' => 'root',
        'charset' => 'utf8'
    ]
]);

$app->register(new ServiceControllerServiceProvider());

$app->before(function(Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// routes

$routes = $app['controllers_factory'];

$routes->post('/users', 'users.controller:register');
$routes->get('/users/me', 'users.controller:getUser');
$routes->put('/users/me', 'users.controller:updateUser');

$routes->get('/users/me/accounts', 'accounts.controller:getAccounts');
$routes->post('/users/me/accounts', 'accounts.controller:createAccount');
$routes->get('/users/me/accounts/{id}', 'accounts.controller:getAccountByID');
$routes->delete('/users/me/accounts/{id}', 'accounts.controller:deleteAccountByID');

$routes->get('/users/me/accounts/{id}/transactions', 'transactions.controller:getTransactions');
$routes->post('/users/me/accounts/{id}/transactions', 'transactions.controller:createTransaction');
$routes->delete('/users/me/accounts/{id}/transactions/{trans}', 'transactions.controller:deleteTransaction');

$routes->get('/users/me/reports', 'reports.controller:getReport');

$routes->get('/categories', 'categories.controller:getCategories');

$app->mount('/api', $routes);