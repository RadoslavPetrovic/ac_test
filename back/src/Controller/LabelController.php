<?php

namespace App\Controller;

use App\Repository\LabelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LabelController extends AbstractController
{
	#[Route('/labels', name: 'app_labels', methods: ['GET'])]
	public function getLabels(LabelRepository $labelRepository, SerializerInterface $serializer): JsonResponse
	{
		$labels = $labelRepository->findAll();

		if (!$labels) {
			throw $this->createNotFoundException(
				'No labels found'
			);
		}

		return (new JsonResponse())->setContent($serializer->serialize($labels, 'json', ['groups' => ['label']]));
	}

	#[Route('/label/{id}', name: 'app_label', methods: ['GET'])]
	public function getLabel(int $id, LabelRepository $labelRepository, SerializerInterface $serializer): JsonResponse
	{
		$label = $labelRepository->find($id);

		if (!$label) {
			throw $this->createNotFoundException(
				'No label found'
			);
		}


		return (new JsonResponse())->setContent($serializer->serialize($label, 'json', ['groups' => ['label']]));
	}
}