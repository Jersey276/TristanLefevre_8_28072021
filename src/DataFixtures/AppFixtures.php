<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const USERS = [
        ['test','$2y$13$foksb6pbKL1xzcTdORwCU.AccEh4b0ly3YF7n7OnNYIvGLqZjlWrK','test@test.fr', ['ROLE_USER'], 'plainPassword' => 'test'],
        ['admin','$2y$13$634vIcdLGraX4qoPNiErReje593QvkiguJTv4DxRYH1u/l8am32pm','admin@test.fr', ['ROLE_ADMIN'], 'plainPassword' => 'admin']
    ];

    const TASKS = [
        ['anonymous task','anonymous content',null],
        ['user task','user content','test'],
        ['admin task','admin content','admin']
    ];
    public function load(ObjectManager $manager) : void
    {
        foreach (self::USERS as $userdata) {
            list($pseudo, $password, $email, $role) = $userdata;
            $user = new User();
            $user->setUsername($pseudo);
            $user->setPassword($password);
            $user->setRoles($role);
            $user->setEmail($email);
            $manager->persist($user);
        }
        $manager->flush();

        foreach (self::TASKS as $userTasks) {
            list($title, $content, $author) = $userTasks;
            $task = new Task();
            $task->setTitle($title);
            $task->setContent($content);
            if ($author != null) {
                $task->setAuthor($manager->getRepository(User::class)->findOneBy(['username' => $author]));
            }
            $manager->persist($task);
        }
        $manager->flush();
    }
}
