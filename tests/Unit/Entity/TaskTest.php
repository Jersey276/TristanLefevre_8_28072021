<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp() : void
    {
        parent::setUp();
        $this->task = new Task();
    }
    
    public function testCreatedAt() : void
    {
        $task = $this->task;

        $this->assertInstanceOf(Datetime::class, $task->getCreatedAt());
        $dumpCreatedAt = new DateTime();
        $task->setCreatedAt($dumpCreatedAt);
        $this->assertSame($dumpCreatedAt, $task->getCreatedAt());
    }

    public function testTitle() : void
    {
        $task = $this->task;
        $title = "titre tache";
        $task->setTitle($title);
        $this->assertSame($title, $task->getTitle());

    }

    public function testContent() : void
    {
        $task = $this->task;
        $content ="Lorem ipsum dolor sit amet";
        $task->setContent($content);
        $this->assertSame($content, $task->getContent());
    }

    public function testIsDone() : void
    {
        $task = $this->task;
        $this->assertFalse($task->isDone());
        $task->toggle(true);
        $this->assertTrue($task->isDone());
    }
}
