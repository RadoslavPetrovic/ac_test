<?php

namespace App\Controller;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
	#[Route('/register', name: 'app_register')]
	public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer): JsonResponse
	{
		$newUser = json_decode($request->getContent(), true);

		$user = new User();

		$hashedPassword = $passwordHasher->hashPassword(
			$user,
			$newUser['password']
		);
		$user->setPassword($hashedPassword);
		$user->setName($newUser['name']);
		$user->setUsername($newUser['username']);
		$user->setAvatarUrl($newUser['avatarUrl']);

		return (new JsonResponse())->setContent($serializer->serialize($user, 'json', ['groups' => ['user']]));
	}

	#[Route('/login_check', name: 'app_check')]
	public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
	{
		return new JsonResponse(['token' => $JWTManager->create($user)]);
	}

}