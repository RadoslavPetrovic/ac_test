<?php

namespace App\Tests;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
	public function testIsCompleted(): void
	{
		$task = new Task();
		$this->assertEmpty($task->getCompletedOn());

		$task->setIsCompleted(true);
		$this->assertNotEmpty($task->getCompletedOn());

		$task->setIsCompleted(false);
		$this->assertEmpty($task->getCompletedOn());
	}

	public function testSetTaskWithParameters(): void
	{
		$data = json_decode('{
      "id": 3,
      "name": "Creating and implementing design system components",
      "isCompleted": false,
      "taskListId": 1,
      "position": 1,
      "startOn": "2023-05-08T00:00:00.000Z",
      "dueOn": "2023-09-10T00:00:00.000Z",
      "labels": [],
      "openSubtasks": 2,
      "commentsCount": 7,
      "assignee": [1],
      "isImportant": false,
      "completedOn": null
    }', true);

		$task = new Task();
		$task->setTaskWithParameters($data);

		$this->assertNotEmpty($task->getName());
		$this->assertNotEmpty($task->getPosition());
		$this->assertNotEmpty($task->getStartOn());
	}
}
