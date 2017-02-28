<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\LocaleServiceProvider;

use Doctrine\Bundle\DoctrineBundle\Registry;

use CurrencyManager\CurrencyManager;
use DollarAndEuro\DollarAndEuro;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new LocaleServiceProvider());
$app['doctrine'] = function($app) {
    return new CurrencyManager($app, new DollarAndEuro());
};
$app['mysql_config'] =
    [
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'currency',
        'user' => 'root',
        'password' => 'gbhfn9013969',
        'charset' => 'utf8',
        'port' => 3306
    ];
$app['conn'] = \Doctrine\DBAL\DriverManager::getConnection($app['mysql_config'], new \Doctrine\DBAL\Configuration());

return $app;
