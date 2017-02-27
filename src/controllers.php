<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Please set to false in a production environment
$app['debug'] = true;

$app->match('/', function (Request $request) use ($app) {
    $content = str_replace("\r\n", " ",  file_get_contents('https://sdw.ecb.europa.eu//quickviewexport.do?SERIES_KEY=120.EXR.D.USD.EUR.SP00.A&type=csv'));
    $csvFile = array_slice(str_getcsv($content), 5);
    $config = new \Doctrine\DBAL\Configuration();
    $connectionParams = array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'currency',
        'user' => 'root',
        'password' => 'gbhfn9013969',
        'charset' => 'utf8',
        'port' => 3306
    );
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    $count =$conn->fetchAll('SELECT * FROM dollar_and_euro');
    if (count($count) == 0) {
        foreach ($csvFile as $key => $value) {
            $explode = explode(" ", $value);
            if (is_array($explode)) {
                $conn->exec('INSERT INTO `dollar_and_euro` (`date`, `course`) VALUES ('.$conn->quote($explode[1]).','. $conn->quote($explode['0']).')');
            }
        }
    } else {
        $max =$conn->fetchAssoc('SELECT MAX(date) FROM dollar_and_euro');
        $maxDate = strtotime($max['MAX(date)']);
       foreach ($csvFile as $key => $value) {
           $explode = explode(" ", $value);
           if (is_array($explode) && isset($explore[1])) {
               $currentDate = strtotime(date($explode[1]));
               if ($currentDate > $maxDate) {
                   $conn->exec('INSERT INTO `dollar_and_euro` (`date`, `course`) VALUES ('.$conn->quote($explode[1]).','. $conn->quote($explode['0']).')');

               }
           }
       }
    }
    $data = array(
        'sum' => 'Введите сумму',
        'date' => 'Выберите дату',
    );
    /**
     * @var \Symfony\Component\Form\Form $form
     */
    $form = $app['form.factory']->createBuilder(FormType::class, $data)
        ->add('sum')
        ->add('Date', DateType::class)
        ->add('save', SubmitType::class, array('label' => 'Look data'))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        $chooseDate = $data['Date']->format('Y-m-d');
        $result =$conn->fetchColumn('SELECT course FROM dollar_and_euro WHERE `date`='.$conn->quote($chooseDate) );
        if (count($result) == 0 ) {
           $result = 'Извините данных за это число нет';
        } else {
            $result = 'На '.$chooseDate.' при курсе '.$result.' сумма составит '.$data['sum'] * $result;
        }

        // redirect somewhere
        return $app['twig']->render('result.html.twig', array('result' => $result));
    }

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