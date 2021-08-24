<?php

namespace App\Tests\Unit\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserManagerTest extends KernelTestCase
{
    private static EntityManager $doctrine;
    private static UserManager $manager;
    private User $user;
    private String $username = "dumpTest", $password = "test", $email = "dump@test.fr";
    private array $roles = ["ROLE_USER"]; 

    protected function setUp() : void
    {
        parent::setUp();

        self::bootKernel();
        self::$doctrine = self::getContainer()->get('doctrine')->getManager();
        self::$manager = new UserManager(self::$doctrine, self::getContainer()->get('security.user_password_hasher'));

        $user = new User();
        $user->setUsername($this->username);
        $user->setEmail($this->email);
        $user->setPassword($this->password);
        $user->setRoles($this->roles);
        $this->user = $user;
    }
    
    
    public function testSuccessSave() : void
    {
        $this->assertTrue(self::$manager->save($this->user));
        
        /** @var User $user */
        $user = self::$doctrine->getRepository(User::class)->findOneBy(['username' => $this->user->getUserIdentifier()]);
        $this->assertEquals($this->username, $user->getUserIdentifier());
        $this->assertTrue(self::getContainer()->get('security.user_password_hasher')->isPasswordValid($user, $this->password));
        $this->assertEquals($this->email, $user->getEmail());
    }

    public function testErrorSave() : void
    {
        $user = new User();
        $user->setPassword('test');
        $this->assertFalse(self::$manager->save($user));
    }

    public function testDuplicateSave() : void
    {
        $user = new User();
        $user->setUsername('test1');
        $user->setPassword('test');
        $user->setEmail('test@test.fr');
        $user->setRoles($this->roles);
        $this->assertFalse(self::$manager->save($user));
    }

    public function testSuccessUpdate() : void
    {
        $dumpUser = self::$doctrine->getRepository(User::class)->findOneBy(['username' => $this->username]);
        $dumpUser->setUsername('testdump');
        $dumpUser->setPassword('test');
        $dumpUser->setEmail('alpha@test.fr');
        $dumpUser->setRoles(['ROLE_ADMIN']);
        $this->assertTrue(self::$manager->update($dumpUser));
        /** @var User $user */
        $user = self::$doctrine->find(User::class, $dumpUser->getId());

        $this->assertEquals('testdump', $user->getUserIdentifier());
        $this->assertTrue(self::getContainer()->get('security.user_password_hasher')->isPasswordValid($user, 'test'));
        $this->assertEquals('alpha@test.fr', $user->getEmail());
        $this->assertEquals(['ROLE_ADMIN','ROLE_USER'], $user->getRoles());
    }
    public function testDuplicateUpdate() : void
    {
        /** @var User $user */
        $dumpUser = self::$doctrine->getRepository(User::class)->findOneBy(['username' => 'testdump']);
        $dumpUser->setUsername('test');
        $this->assertFalse(self::$manager->update($dumpUser));
    }
}
