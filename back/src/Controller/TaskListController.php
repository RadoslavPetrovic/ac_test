<?php

namespace App\Controller;

use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskListController extends AbstractController
{
	#[Route('/taskLists', name: 'app_taskLists', methods: ['GET'])]
	public function getTaskLists(TaskListRepository $taskListRepository, SerializerInterface $serializer): JsonResponse
	{
		$taskLists = $taskListRepository->findAll();

		if (!$taskLists) {
			throw $this->createNotFoundException('No taskLists found');
		}

		return (new JsonResponse())->setContent($serializer->serialize($taskLists, 'json'));
	}

	#[Route('/taskList/{id}', name: 'app_taskList', methods: ['GET'])]
	public function getTaskList(int $id, TaskListRepository $taskListRepository, SerializerInterface $serializer): JsonResponse
	{
		$taskList = $taskListRepository->find($id);

		if (!$taskList) {
			throw $this->createNotFoundException('No taskList found');
		}

		return (new JsonResponse())->setContent($serializer->serialize($taskList, 'json'));
	}

	#[Route('/taskList/complete/{id}', name: 'app_taskList_complete', methods: ['PUT'])]
	public function completeTaskList(int $id, TaskListRepository $taskListRepository, TaskRepository $taskRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
	{
		$taskList = $taskListRepository->find($id);

		if (!$taskList) {
			throw $this->createNotFoundException('No taskList found');
		}

		$taskList->setIsCompleted(true);
		$tasks = $taskRepository->findByTaskListId($id);

		foreach ($tasks as $task) {
			$task->setIsCompleted(true);
			$entityManager->persist($task);
		}

		$entityManager->persist($taskList);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($taskList, 'json'));
	}

	#[Route('/taskList', name: 'app_taskList_update', methods: ['PUT'])]
	public function deleteTaskList(Request $request, TaskListRepository $taskListRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
	{
		$newTaskList = json_decode($request->getContent(), true);
		$taskList = $taskListRepository->find($newTaskList['id']);

		if (!$taskList) {
			throw $this->createNotFoundException('No taskList found');
		}

		$taskList->setTaskListWithParameters($newTaskList);
		$entityManager->persist($taskList);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($taskList, 'json'));
	}

	#[Route('/taskList', name: 'app_taskList_create', methods: ['POST'])]
	public function createTaskList(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer): Response
	{
		$newTaskList = json_decode($request->getContent(), true);

		$taskList = new TaskList();
		$taskList->setTaskListWithParameters($newTaskList);

		$errors = $validator->validate($taskList);
		if (count($errors) > 0) {
			return new Response((string)$errors, 400);
		}

		$entityManager->persist($taskList);
		$entityManager->flush();

		return (new JsonResponse())->setContent($serializer->serialize($taskList, 'json'));
	}
}
