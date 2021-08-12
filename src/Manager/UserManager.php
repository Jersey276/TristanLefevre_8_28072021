<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends AbstractManager
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $doctrine,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::initialize($doctrine);
        $this->passwordHasher = $passwordHasher;
    }

    public function save(User $user)
    {
        $user->setPassword($this->hashPassword($user));
        $this->doctrine->persist($user);
        try {
            $this->doctrine->flush();
            return true;
        } catch (ORMException $e){
            return false;
        }
    }

    public function update(User $user)
    {
        try {
            $user->setPassword($this->hashPassword($user));
            $this->doctrine->flush();
            return true;
        } catch (ORMException $e){
            return false;
        }
    }

    private function hashPassword(User $user) : string
    {
        return $this->passwordHasher->hashPassword($user, $user->getPassword());
    }
}
