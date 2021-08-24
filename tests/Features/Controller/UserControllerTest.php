<?php

namespace App\Tests\Features\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private static EntityManagerInterface $doctrine;
    private User $user;

    public function setUp() : void
    {
        $this->client = static::createClient();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();
        $this->user = self::$doctrine->find(User::class, 2);
    }

    public function testRegister(): User
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Créer')->form();
        $form['user[username]']->setValue('testalpha');
        $form['user[password][first]']->setValue('testalpha');
        $form['user[password][second]']->setValue('testalpha');
        $form['user[email]']->setValue('test3@test.fr');
        $form['user[roles][1]']->tick();
        $crawler = $this->client->submit($form);

        
        $this->assertSame('/login', parse_url($crawler->getBaseHref(), PHP_URL_PATH));

        /** @var User $user */
        $user = self::$doctrine->getRepository(User::class)->findOneBy(['username' => 'testalpha']);
        $this->assertEquals('testalpha', $user->getUserIdentifier());
        $this->assertEquals('test3@test.fr', $user->getEmail());
        $this->assertEquals(['ROLE_USER','ROLE_ADMIN'], $user->getRoles());
        $user->setPassword('testalpha');

        return $user;
    }

    /**
     * @depends testRegister
     */
    public function testListUser(User $user) : void
    {
        $this->client->loginUser($this->user);
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('html:contains("'. $user->getId().'")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("'. $user->getUserIdentifier().'")')->count());
    }

    /**
     * @depends testRegister
     */
    public function testmodifyUser(User $user): void
    {
        $this->client->followRedirects();
        
        $this->client->loginUser(self::$doctrine->find(User::class,2));

        $crawler = $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('html:contains("'. $this->user->getUserIdentifier() .'")')->count());

        $crawler = $this->client->request('GET','/users/'.$user->getId().'/edit');
        $this->assertSame(1, $crawler->filter('form')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]']->setValue('testbeta');
        $form['user[password][first]']->setValue('testbeta');
        $form['user[password][second]']->setValue('testbeta');
        $form['user[email]']->setValue('beta@test.fr');
        $form['user[roles][0]']->tick();
        $form['user[roles][1]']->untick();
        $crawler = $this->client->submit($form);

        /** @var User $user */
        $user = self::$doctrine->getRepository(User::class)->findOneBy(['username' => 'testbeta']);
        $this->assertEquals('testbeta', $user->getUserIdentifier());
        $this->assertEquals('beta@test.fr', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
