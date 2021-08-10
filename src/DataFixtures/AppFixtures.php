<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const USERS = [
        ['test1','$2y$13$qb7jrDY7HZzf4Yv.lTzeKOZmlPDCfANJWLynrPMZyGDVxlHFKp9re','test@test.fr', ['ROLE_USER']],
        ['test2','$2y$13$ynDKxG0E2QfyXAptd/.FD.b.6nl9miG.NPT3unkapXBYCh44puafC','test1@test.fr', ['ROLE_USER']]
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::USERS as $userdata)
        {
            list($pseudo, $password, $email, $role) = $userdata;
            $user = new User();
            $user->setUsername($pseudo);
            $user->setPassword($password);
            $user->setRoles($role);
            $user->setEmail($email);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
