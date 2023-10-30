<?php

namespace App\DataFixtures;

use App\Entity\Label;
use App\Entity\Task;
use App\Entity\TaskList;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager): void
	{
		$dataFile = file_get_contents(getcwd() . '/src/DataFixtures/data.json');
		$data = json_decode($dataFile, true);

		$users = [];

		foreach ($data['users'] as $user) {
			$newUser = new User();
			$newUser->setName($user['name']);
			$newUser->setAvatarUrl($user['avatarUrl']);
			$newUser->setUsername($user['username']);
			$newUser->setPassword($user['password']);

			$manager->persist($newUser);
			$manager->flush();
			$users[$newUser->getId()] = $newUser;
		}

		$labels = [];

		foreach ($data['labels'] as $label) {
			$newLabel = new Label();
			$newLabel->setColor($label['color']);

			$manager->persist($newLabel);
			$manager->flush();
			$labels[$newLabel->getId()] = $newLabel;
		}

		foreach ($data['task_lists'] as $taskList) {
			$newTaskList = new TaskList();
			$newTaskList->setTaskListWithParameters($taskList);

			$manager->persist($newTaskList);
		}

		foreach ($data['tasks'] as $task) {
			$newTask = new Task();
			$newTask->setTaskWithParameters($task);
			foreach ($task['assignee'] as $id) {
				$newTask->addAssignee($users[$id]);
			}
			foreach ($task['labels'] as $id) {
				$newTask->addLabel($labels[$id]);
			}
			$manager->persist($newTask);
		}

		$manager->flush();
	}
}
