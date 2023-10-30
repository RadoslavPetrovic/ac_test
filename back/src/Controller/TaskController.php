<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
	#[Route('/tasks', name: 'app_tasks', methods: ['GET'])]
	public function getTasks(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
	{
		$tasks = $taskRepository->findAll();

		if (!$tasks) {
			throw $this->createNotFoundException('No tasks found');
		}

		return (new JsonResponse())->setContent($serializer->serialize($tasks, 'json', ['groups' => ['task']]));
	}

	#[Route('/task/{id}', name: 'app_task', methods: ['GET'])]
	public function getTask(int $id, TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
	{
		$task = $taskRepository->find($id);

		if (!$task) {
			throw $this->createNotFoundException('No tasks found');
		}

		return (new JsonResponse())->setContent($serializer->serialize($task, 'json', ['groups' => ['task']]));
	}

	#[Route('/task', name: 'app_task_update', methods: ['PUT'])]
	public function updateTask(Request $request, TaskRepository $taskRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer): Response
	{
		$newTask = json_decode($request->getContent(), true);
		$task = $taskRepository->find($newTask['id']);

		if (!$task) {
			throw $this->createNotFoundException('No tasks found');
		}

		$task->setTaskWithParameters($newTask);

		foreach ($newTask['assignee'] as $assigneeId) {
			$assignee = $userRepository->find($assigneeId);
			$task->addAssignee($assignee);
		}

		$errors = $validator->validate($newTask);
		if (count($errors) > 0) {
			return new Response((string)$errors, 400);
		}

		$entityManager->persist($task);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($task, 'json', ['groups' => ['task']]));
	}

	#[Route('/task', name: 'app_task_create', methods: ['POST'])]
	public function createTask(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer): Response
	{
		$newTask = json_decode($request->getContent(), true);
		$task = new Task();
		$task->setTaskWithParameters($newTask);

		foreach ($newTask['assignee'] as $assigneeId) {
			$assignee = $userRepository->find($assigneeId);
			$task->addAssignee($assignee);
		}

		$errors = $validator->validate($task);
		if (count($errors) > 0) {
			return new Response((string)$errors, 400);
		}

		$entityManager->persist($task);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($task, 'json', ['groups' => ['task']]));
	}

	#[Route('/task/complete/{id}', name: 'app_task_complete', methods: ['PUT'])]
	public function completeTask(int $id, TaskRepository $taskRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
	{
		$task = $taskRepository->find($id);

		if (!$task) {
			throw $this->createNotFoundException('No tasks found');
		}

		$task->setIsCompleted(true);
		$entityManager->persist($task);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($task, 'json', ['groups' => ['task']]));
	}

	#[Route('/task/reopen', name: 'app_task_reopen', methods: ['PUT'])]
	public function reopenTask(Request $request, TaskRepository $taskRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		$task = $taskRepository->find($data['id']);

		if (!$task) {
			throw $this->createNotFoundException('No tasks found');
		}

		if ($data['taskListId']) {
			$task->setTaskListId($data['taskListId']);
		}

		$task->setIsCompleted(false);
		$entityManager->persist($task);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($task, 'json', ['groups' => ['task']]));
	}

	#[Route('/task/updateTaskPosition', name: 'app_task_updateTaskPosition', methods: ['PUT'])]
	public function updateTaskPosition(Request $request, TaskRepository $taskRepository, EntityManagerInterface $entityManager): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		$taskListIds = array_keys($data);

		foreach ($taskListIds as $taskListId) {
			foreach ($data[$taskListId] as $key => $taskId) {
				$task = $taskRepository->find($taskId);
				$task->setPosition($key);
				$task->setTaskListId($taskListId);
				$entityManager->persist($task);
			}
		}

		$entityManager->flush();

		return (new JsonResponse())->setContent(json_encode($data));
	}
}
