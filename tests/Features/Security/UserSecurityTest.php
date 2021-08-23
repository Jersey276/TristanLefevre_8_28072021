<?php

namespace App\Tests\Features\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserSecurityTest extends WebTestCase

{
    private KernelBrowser $client;
    private User $user;
    private static EntityManagerInterface $doctrine;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->client = static::createClient();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();


        $this->user = self::$doctrine->find(User::class, 1);
        $this->client->loginUser($this->user);
    }

    /**
     * @dataProvider dataUser
     */
    public function testRoleUser($url, $code) : void
    {               
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame($code, $this->client->getResponse()->getStatusCode());


    }

    public function dataUser() : array
    {
        return [
            ['/', 200],
            ['/users', 403],
            ['/users/create', 200],
            ['/tasks',200],
            ['tasks/done',200],
            ['/tasks/create',200],
            ['tasks/1/edit',403],
            ['tasks/2/edit',200],
            ['tasks/3/edit',403]
        ];
    }

}
