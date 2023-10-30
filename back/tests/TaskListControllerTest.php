<?php

use App\Controller\TaskListController;
use App\Entity\Task;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class TaskListControllerTest extends TestCase
{
	public function testGetTaskLists(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('findAll')->willReturn([new TaskList(), new TaskList()]);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$taskLists = $taskListController->getTaskLists($taskListRepository, $serializerInterface);

		$this->assertSame($taskLists->getStatusCode(), 200);
		$this->assertSame($taskLists->getContent(), '{"ok": "ok"}');
	}

	public function testGetTaskListsEmpty(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('findAll')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$taskListController->getTaskLists($taskListRepository, $serializerInterface);
	}

	public function testGetTaskList(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('find')->willReturn(new TaskList());
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$taskLists = $taskListController->getTaskList(1, $taskListRepository, $serializerInterface);

		$this->assertSame($taskLists->getStatusCode(), 200);
		$this->assertSame($taskLists->getContent(), '{"ok": "ok"}');
	}

	public function testGetTaskListEmpty(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('find')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$taskListController->getTaskList(1, $taskListRepository, $serializerInterface);
	}

	public function testCompleteTaskList(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('find')->willReturn(new TaskList());

		$taskRepository = $this->createMock(TaskRepository::class);
		$taskRepository->expects(self::once())->method('findByTaskListId')->willReturn([new Task(), new Task()]);

		$entityManagerInterface = $this->createMock(EntityManagerInterface::class);
		$entityManagerInterface->expects(self::exactly(3))->method('persist');
		$entityManagerInterface->expects(self::exactly(1))->method('flush');

		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$taskList = $taskListController->completeTaskList(1, $taskListRepository, $taskRepository, $entityManagerInterface, $serializerInterface);

		$this->assertSame($taskList->getStatusCode(), 200);
		$this->assertSame($taskList->getContent(), '{"ok": "ok"}');
	}

	public function testCompleteTaskListEmpty(): void
	{
		$taskListController = new TaskListController();

		$taskListRepository = $this->createMock(TaskListRepository::class);
		$taskListRepository->expects(self::once())->method('find')->willReturn(null);

		$taskRepository = $this->createMock(TaskRepository::class);
		$taskRepository->expects(self::never())->method('find')->willReturn(null);

		$entityManagerInterface = $this->createMock(EntityManagerInterface::class);
		$entityManagerInterface->expects(self::never())->method('persist');

		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$taskListController->completeTaskList(1, $taskListRepository, $taskRepository, $entityManagerInterface, $serializerInterface);
	}
}
