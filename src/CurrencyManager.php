<?php

namespace CurrencyManager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use DollarAndEuro\DollarAndEuro;
use Silex\Application;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurrencyManager
{
    /** @var  Registry $doctrine */
    protected $app;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get all data in table
     *
     * @return array
     */
    public function getAllData()
    {
        $count = $this->doctrine->getRepository('DollarAndEuro')->findAll();

        return $count;
    }

    /**
     * Add data in table. First run application
     *
     * @param array $csvFile
     */
    public function insertData($csvFile)
    {
        $em = $this->doctrine->getManager();
        foreach ($csvFile as $key => $value) {
            $explode = explode(" ", $value);
            if (is_array($explode)) {
                $currency = new DollarAndEuro();
                $currency->setCourse($explode['0']);
                $currency->setDate(new \DateTime($explode[1]));
                $em->persist($currency);
            }
        }
        $em->flush();
    }

    /**
     * Add new data for table id this need.
     *
     * @param array $max
     * @param array $csvFile
     */
    public function insertDataIfExist ($max, $csvFile)
    {
        $em = $this->doctrine->getManager();
        $maxDate = strtotime($max['MAX(date)']);
        foreach ($csvFile as $key => $value) {
            $explode = explode(" ", $value);
            if (is_array($explode) && isset($explore[1])) {
                $currentDate = strtotime(date($explode[1]));
                if ($currentDate > $maxDate) {
                    $currency = new DollarAndEuro();
                    $currency->setCourse($explode['0']);
                    $currency->setDate(new \DateTime($explode[1]));
                    $em->persist($currency);
                }
            }
        }
        $em->flush();
    }

    /**
     * @param DateTime $chooseDate
     *
     * @return array
     */
    protected function getCurrentDate($chooseDate)
    {
        $currentDate = $this
            ->doctrine
            ->getRepository('DollarAndEuro')
            ->findOneBy(
                [
                    'date' => $chooseDate
                ]
            );

        return $currentDate;
    }

    /**
     * Render form ore render answer
     *
     * @param Request $request
     * @param Application $app
     *
     * @return \Symfony\Component\Form\Form
     */
    public function render(Request $request, Application $app)
    {
        /**
         * @var \Symfony\Component\Form\Form $form
         */
        $form = $app['form.factory']->createBuilder(FormType::class, [])
            ->add('sum')
            ->add('Date', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Look data'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
//        $chooseDate = $data['Date']->format('Y-m-d');
//        $result =$app['conn']->fetchColumn('SELECT course FROM dollar_and_euro WHERE `date`='.$app['conn']->quote($chooseDate) );
            $chooseDate = $data['Date'];
            $result = $app['doctrine']->getCurrentDate($chooseDate);
            if ($result == false ) {
                $response = [];
            } else {
                $response =
                    [
                        'chooseDate' => $chooseDate,
                        'sum' => $data['sum'] * $result,
                        'result' => $result
                    ];
            }

            // redirect somewhere
            return $app['twig']->render('result.html.twig', array('response' => $response));
        }

        return $form;
    }

}