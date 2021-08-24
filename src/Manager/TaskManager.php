<?php

namespace App\Manager;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class TaskManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $doctrine)
    {
        parent::initialize($doctrine);
    }

    public function save(Task $task, User $user) : bool
    {
        $task->setAuthor($user);
        $this->doctrine->persist($task);
        try {
            $this->doctrine->flush();
            return true;
        } catch (\Exception $e){
            return false;
        }
    }

    public function update(Task $task) : bool
    {
        $this->doctrine->flush();
        return true;
    }

    public function remove(Task $task) : bool
    {
        $this->doctrine->remove($task);
        $this->doctrine->flush();
        return true;
    }
}