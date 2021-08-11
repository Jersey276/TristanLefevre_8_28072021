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

    public function save(Task $task)
    {
        $this->doctrine->persist($task);
        try {
            $this->doctrine->flush();
            return true;
        } catch (ORMException $e){
            return false;
        }
    }

    public function update(Task $task)
    {
        try {
            $this->doctrine->flush();
            return true;
        } catch (ORMException $e){
            return false;
        }
    }

    public function remove(Task $task)
    {
        $this->doctrine->remove($task);
        try {
            $this->doctrine->flush();
            return true;
        } catch (ORMException $e){
            return false;
        }
    }
}