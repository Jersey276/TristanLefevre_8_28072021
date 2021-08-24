<?php

namespace App\Tests\Features\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private static EntityManagerInterface $doctrine;
    private User $user;

    public function setUp() : void
    {
        $this->client = static::createClient();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();
        $this->user = self::$doctrine->find(User::class, 1);
        $this->client->loginUser($this->user);
    }
    public function testCreateTask(): void
    {
        $crawler = $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Créer une nouvelle tâche');

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->filter('form')->Count());
        
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'testTask';
        $form['task[content]'] = 'contentTask';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('html:contains("testTask")')->Count());
        $this->assertSame(1, $crawler->filter('html:contains("contentTask")')->Count());
        $this->assertSame(1, $crawler->filter('html:contains("'.$this->user->getUserIdentifier().'")')->Count());
    }

    public function testUpdateTask() : void
    {
        $crawler = $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Consulter la liste des tâches à faire');

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->filter('html:contains("testTask")')->Count());
        $this->assertSame(1, $crawler->filter('html:contains("contentTask")')->Count());

        $crawler = $this->client->clickLink('testTask');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form();

        $this->assertSame("testTask",$form['task[title]']->getValue());
        $this->assertSame("contentTask",$form['task[content]']->getValue());

        $form['task[title]'] = 'testTask1';
        $form['task[content]'] = 'contentTask1';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $this->assertSame(1, $crawler->filter('html:contains("testTask1")')->Count());
        $this->assertSame(1, $crawler->filter('html:contains("contentTask1")')->Count());
    }

    public function testRemoveTask() : void
    {
        $crawler = $this->client->request('GET', '/tasks');
        $crawler = $this->client->clickLink('testTask1');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Supprimer')->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame('/tasks', parse_url($crawler->getBaseHref(), PHP_URL_PATH));
        $this->assertSame(0, $crawler->filter('html:contains("alert alert-success")')->count());
        $this->assertSame(0, $crawler->filter('html:contains("testTask1")')->count());
        $this->assertSame(0, $crawler->filter('html:contains("contentTask1")')->count());
    }

    public function testToggleTask() : void
    {
        $crawler = $this->client->request('GET', '/tasks');
        
        $this->assertSame(1, $crawler->filter('html:contains("user task")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("test")')->count());

        $crawler = $this->client->request('GET', '/tasks/2/toggle');
        $crawler = $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(1, $crawler->filter('html:contains("user task")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("test")')->count());

        $crawler = $this->client->request('GET', '/tasks/2/toggle');
        $crawler = $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/tasks');
        
        $this->assertSame(1, $crawler->filter('html:contains("user task")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("test")')->count());
    }
}
