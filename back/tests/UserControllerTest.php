<?php

use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class UserControllerTest extends TestCase
{
	public function testGetUsers(): void
	{
		$userController = new UserController();

		$userRepository = $this->createMock(UserRepository::class);
		$userRepository->expects(self::once())->method('findAll')->willReturn([new User(), new User()]);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$users = $userController->getusers($userRepository, $serializerInterface);

		$this->assertSame($users->getStatusCode(), 200);
		$this->assertSame($users->getContent(), '{"ok": "ok"}');
	}

	public function testGetUsersEmpty(): void
	{
		$userController = new UserController();

		$userRepository = $this->createMock(UserRepository::class);
		$userRepository->expects(self::once())->method('findAll')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$userController->getUsers($userRepository, $serializerInterface);
	}

	public function testGetUser(): void
	{
		$userController = new UserController();

		$userRepository = $this->createMock(UserRepository::class);
		$userRepository->expects(self::once())->method('find')->willReturn(new User());
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::once())->method('serialize')->willReturn('{"ok": "ok"}');

		$users = $userController->getUserData(1, $userRepository, $serializerInterface);

		$this->assertSame($users->getStatusCode(), 200);
		$this->assertSame($users->getContent(), '{"ok": "ok"}');
	}

	public function testGetUserEmpty(): void
	{
		$userController = new UserController();

		$userRepository = $this->createMock(UserRepository::class);
		$userRepository->expects(self::once())->method('find')->willReturn(null);
		$serializerInterface = $this->createMock(SerializerInterface::class);
		$serializerInterface->expects(self::never())->method('serialize')->willReturn('{"ok": "ok"}');

		$this->expectException(NotFoundHttpException::class);

		$userController->getUserData(1, $userRepository, $serializerInterface,);
	}
}
