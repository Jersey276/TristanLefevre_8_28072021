<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager
{
    protected EntityManagerInterface $doctrine;

    public function initialize(EntityManagerInterface $doctrine) : void
    {
        $this->doctrine = $doctrine;
    }
}
