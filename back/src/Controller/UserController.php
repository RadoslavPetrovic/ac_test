<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
	#[Route('/users', name: 'app_users', methods: ['GET'])]
	public function getUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
	{
		$users = $userRepository->findAll();

		if (!$users) {
			throw $this->createNotFoundException(
				'No users found'
			);
		}

		return (new JsonResponse())->setContent($serializer->serialize($users, 'json', ['groups' => ['user']]));
	}

	#[Route('/user/{id}', name: 'app_user', methods: ['GET'])]
	public function getUserData(int $id, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
	{
		$user = $userRepository->find($id);

		if (!$user) {
			throw $this->createNotFoundException(
				'No user found'
			);
		}

		return (new JsonResponse())->setContent($serializer->serialize($user, 'json', ['groups' => ['user']]));
	}
}