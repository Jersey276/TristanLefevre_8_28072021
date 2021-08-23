<?php

namespace App\Tests\Features\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private User $user, $admin;
    private KernelBrowser $client;
    private static EntityManagerInterface $doctrine;

    protected function setUp() : void
    {
        parent::setUp();
        $this->client = static::createClient();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();
        $this->client->followRedirects();
        $this->user = self::$doctrine->find(User::class, 1);
    }

    public function testLoginLogout() : void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();

        $form['username']->setValue($this->user->getUserIdentifier());
        $form['password']->setValue(AppFixtures::USERS[0]['plainPassword']);
        $crawler = $this->client->submit($form);
        
        $this->assertSame('/', parse_url($this->client->getRequest()->getUri(), PHP_URL_PATH));
        
        $crawler = $this->client->request('GET','/logout');
        $this->assertNotSame('/', parse_url($this->client->getRequest()->getUri(), PHP_URL_PATH));
    }

    public function testLoginWithRedirect() : void
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame('/login', parse_url($this->client->getRequest()->getUri(), PHP_URL_PATH));

        $form = $crawler->selectButton('Se connecter')->form();

        $form['username']->setValue($this->user->getUserIdentifier());
        $form['password']->setValue(AppFixtures::USERS[0]['plainPassword']);
        $crawler = $this->client->submit($form);

        $this->assertSame('/tasks', parse_url($this->client->getRequest()->getUri(), PHP_URL_PATH));
    }
}
