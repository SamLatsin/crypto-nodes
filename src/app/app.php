<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

include __DIR__ . '/config/bootstrap.php';

$loader = new Loader();
$loader->registerNamespaces([
    'MyApp\Models' => __DIR__ . '/models/',
    'MyApp\Controllers' => __DIR__ . '/controllers/',
]);

$loader->register();
$container = new FactoryDefault();

$container->set('db',function () {
    return new PdoMysql([
        'host'     => 'localhost',
        'username' => '{username}',
        'password' => '{password}',
        'dbname'   => '{dbname}',

    ]);
});
 
$app = new Micro($container);
$app["request"] = new \Phalcon\Http\Request();
require_once __DIR__.'/route/load.php';

try {
    $app->handle($_SERVER["REQUEST_URI"]);
} catch (Exception $e) {
    echo $e->getMessage();
} 
