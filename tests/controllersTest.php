<?php

use Silex\WebTestCase;
use Symfony\Component\DomCrawler\Form;

class controllersTest extends WebTestCase
{
    public function testGetHomepage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $this->assertContains('Заполните', $crawler->filter('body')->text());

        $buttonCrawlerNode = $crawler->selectButton('Look data');
        /** @var Form $form */
        $form = $buttonCrawlerNode->form($this->getData());
        $client->submit($form);
        $this->assertContains('сумма составит', $crawler->filter('body')->text());
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';
        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../src/controllers.php';
        $app['session.test'] = true;

        return $this->app = $app;
    }

    protected function getData()
    {
        return [
            'form[sum]' => 124,
            'form[Date]' => '',
            'form[save]' => '2016-07-11'
        ];

    }
}
