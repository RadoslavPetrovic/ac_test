<?php

use App\Entity\TaskList;
use PHPUnit\Framework\TestCase;

class TaskListTest extends TestCase
{
	public function testSetTaskListWithParameters(): void
	{
		$data = json_decode('    {
      "id": 2,
      "name": "In Progress",
      "openTasks": 3,
      "completedTasks": 0,
      "position": 1,
      "isCompleted": false,
      "isTrashed": false
    }', true);

		$taskList = new TaskList();
		$taskList->setTaskListWithParameters($data);

		$this->assertNotEmpty($taskList->getName());
		$this->assertNotEmpty($taskList->getPosition());
	}
}
