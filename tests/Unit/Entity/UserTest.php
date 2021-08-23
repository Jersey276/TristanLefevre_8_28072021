<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp() : void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testUserName(): void
    {
        $user = $this->user;
        $username = 'test';
        $user->setUsername($username);
        $this->assertSame($username, $user->getUsername());
        $this->assertSame($username, $user->getUserIdentifier());
    }

    public function testPassword():void
    {
        $user = $this->user;
        $password = 'test';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());
    }

    public function testEmail():void
    {
        $user = $this->user;
        $email = 'test@test.fr';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());  
    }
    public function testSalt():void
    {
        $user = $this->user;
        $this->assertNull($user->getSalt());
    }

    public function testRole():void
    {
        $user = $this->user;
        $roles = ['ROLE_USER'];
        $this->assertIsArray($user->getRoles());
        $this->assertSame($roles, $user->getRoles());
        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);
        $this->assertSame(array_merge($roles,['ROLE_USER']),$user->getRoles());
    }

    public function testTask():void
    {
        $user = $this->user;
        $task = new Task();
        $task->setTitle('hello');
        $task->setContent('world');
        $user->addTask($task);
        $this->assertInstanceOf(Collection::class, $user->getTasks());
        $this->assertSame(($user->getTasks())[0], $task);
        $user->removeTask($task);
        $this->assertEquals(0, $user->getTasks()->count());
        }
}
