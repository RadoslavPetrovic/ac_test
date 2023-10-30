<?php

namespace App\Entity;

use App\Repository\TaskListRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: TaskListRepository::class)]
class TaskList
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column]
	private ?int $position = null;

	#[ORM\Column]
	#[SerializedName('isCompleted')]
	private ?bool $is_completed = null;

	#[ORM\Column]
	#[SerializedName('isTrashed')]
	private ?bool $is_trashed = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getPosition(): ?int
	{
		return $this->position;
	}

	public function setPosition(int $position): static
	{
		$this->position = $position;

		return $this;
	}

	public function isIsCompleted(): ?bool
	{
		return $this->is_completed;
	}

	public function isIsTrashed(): ?bool
	{
		return $this->is_trashed;
	}

	public function setTaskListWithParameters(mixed $parameters): TaskList
	{
		if ($parameters['name']) $this->setName($parameters['name']);
		$this->setIsCompleted($parameters['isCompleted']);
		$this->setPosition($parameters['position'] || 0);
		$this->setIsTrashed($parameters['isTrashed']);

		return $this;
	}

	public function setIsCompleted(bool $is_completed): static
	{
		$this->is_completed = $is_completed;

		return $this;
	}

	public function setIsTrashed(bool $is_trashed): static
	{
		$this->is_trashed = $is_trashed;

		return $this;
	}
}
