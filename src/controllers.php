<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use CurrencyManager\CurrencyManager;


// Please set to false in a production environment
$app['debug'] = true;

$app->match('/', function (Request $request) use ($app) {
    $content = str_replace("\r\n", " ",  file_get_contents('https://sdw.ecb.europa.eu//quickviewexport.do?SERIES_KEY=120.EXR.D.USD.EUR.SP00.A&type=csv'));
    $csvFile = array_slice(str_getcsv($content), 5);
    $count = $app['doctrine']->getAllData();
//    $count = $app['conn']->fetchAll('SELECT * FROM dollar_and_euro');
    if (count($count) == 0) {
       $app['doctrine']->insertData($csvFile);
    } else {
        $max =$app['conn']->fetchAssoc('SELECT MAX(date) FROM dollar_and_euro');
       $app['doctrine']->insertDataIfExist($max, $csvFile);
    }
    /**
     * @var \Symfony\Component\Form\Form $form
     */
    $form = $app['doctrine']->render($request, $app);

    return $app['twig']->render('index.html.twig', array('form' => $form->createView()));
})
->bind('homepage')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});