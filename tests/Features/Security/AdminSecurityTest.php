<?php

namespace App\Tests\Features\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminSecurityTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $user;
    private static EntityManagerInterface $doctrine;

    public function setup(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();


        $this->user = self::$doctrine->find(User::class, 2);
        $this->client->loginUser($this->user);
    }

        /**
     * @dataProvider dataAdmin
     */
    public function testRoleAdmin(String $url, int $code): void
    {
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame($code, $this->client->getResponse()->getStatusCode());
    }

    public function dataAdmin() : array
    {
        return [
            ['/', 200],
            ['users', 200],
            ['users/create', 200],
            ['tasks',200],
            ['tasks/done',200],
            ['tasks/create',200],
            ['tasks/1/edit',200],
            ['tasks/2/edit',403],
            ['tasks/3/edit',200]
        ];
    }
}
