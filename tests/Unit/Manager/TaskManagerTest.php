<?php

namespace App\Tests\Unit\Manager;

use App\Entity\Task;
use App\Entity\User;
use App\Manager\TaskManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManager;

class TaskManagerTest extends KernelTestCase
{

    protected static EntityManager $doctrine;
    protected static TaskManager $manager;
    private User $user;
    private Task $task;

    protected function setUp():void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        self::$doctrine = $kernel->getContainer()->get('doctrine')->getManager();
        self::$manager = new TaskManager(self::$doctrine);

        $this->user = self::$doctrine->find(User::class, 1);
        
        // fake Task with data
        $task = new Task();
        $task->setTitle('titre1');
        $task->setContent('content1');
        $this->task = $task;

    }

    public function testSuccessSave(): void
    {
        $this->assertTrue(self::$manager->save($this->task, $this->user));
        /** @var Task $task */
        $task = self::$doctrine->getRepository(Task::class)->findOneBy(['title' => $this->task->getTitle()]);
        $this->assertSame($this->task->getTitle() ,$task->getTitle());
        $this->assertSame($this->task->getContent() ,$task->getContent());
        $this->assertSame($this->task->getAuthor() ,$task->getAuthor());
    }

    public function testErrorSave() : void
    {
        $this->assertFalse(self::$manager->save(new Task(), $this->user));
    }

    public function testSuccessUpdate() : void
    {
        /** @var Task $task */
        $task = self::$doctrine->getRepository(Task::class)->findOneBy(['title' => $this->task->getTitle()]);
        $task->setTitle('titre2');
        $task->setContent('content2');
        $this->assertTrue(self::$manager->update($task));
        /** @var Task $toCheckTask */
        $toCheckTask = self::$doctrine->getRepository(Task::class)->find($task->getId());
        
        //check id is same
        $this->assertSame($task->getId(), $toCheckTask->getId());
        
        //check updated data
        $this->assertNotSame($this->task->getTitle(), $toCheckTask->getTitle());
        $this->assertNotSame($this->task->getContent(), $toCheckTask->getContent());

        //check data match
        $this->assertSame($task->getTitle(), $toCheckTask->getTitle());
        $this->assertSame($task->getContent(), $toCheckTask->getContent());
        
    }

    public function testSuccessRemove() : void
    {
        $this->assertTrue(self::$manager->remove($this->task));
    }

}
