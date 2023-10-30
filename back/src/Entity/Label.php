<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
class Label
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['task', 'label'])]
	private ?int $id = null;

	#[Groups(['label'])]
	#[ORM\Column(length: 9)]
	private ?string $color = null;

	/** @Ignore() */
	#[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'labels')]
	private Collection $task;

	public function __construct()
	{
		$this->task = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getColor(): ?string
	{
		return $this->color;
	}

	public function setColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}

	/**
	 * @return Collection<int, Task>
	 */
	public function getTask(): Collection
	{
		return $this->task;
	}

	public function addTask(Task $task): static
	{
		if (!$this->task->contains($task)) {
			$this->task->add($task);
		}

		return $this;
	}

	public function removeTask(Task $task): static
	{
		$this->task->removeElement($task);

		return $this;
	}
}
