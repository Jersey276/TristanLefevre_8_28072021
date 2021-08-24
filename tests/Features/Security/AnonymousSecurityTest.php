<?php

namespace App\Tests\Features\Security;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousSecurityTest extends WebTestCase
{
    private KernelBrowser $client;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->client = static::createClient();
    }
    
    /**
     * @dataProvider dataAnonymous
     */
    public function testRoleAnonymous(String $url, int $code): void
    {
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame($code, $this->client->getResponse()->getStatusCode());
    }

    public function dataAnonymous() : array
    {
        return [
            ['/', 302],
            ['users', 302],
            ['users/create', 200],
            ['tasks',302],
            ['tasks/done',302],
            ['tasks/create',302],
            ['tasks/1/edit',302],
            ['tasks/2/edit',302],
            ['tasks/3/edit',302]
        ];
    }
}