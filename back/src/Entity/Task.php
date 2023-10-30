<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['task'])]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(['task'])]
	private ?string $name = null;

	#[ORM\Column]
	#[Groups(['task'])]
	#[SerializedName('isCompleted')]
	private ?bool $is_completed = null;

	#[ORM\Column]
	#[Groups(['task'])]
	#[SerializedName('taskListId')]
	private ?int $task_list_id = null;

	#[ORM\Column]
	#[Groups(['task'])]
	private ?int $position = null;

	#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
	#[Groups(['task'])]
	#[SerializedName('startOn')]
	private ?DateTimeInterface $start_on = null;

	#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
	#[Groups(['task'])]
	#[SerializedName('dueOn')]
	private ?DateTimeInterface $due_on = null;

	#[ORM\Column]
	#[Groups(['task'])]
	#[SerializedName('isImportant')]
	private ?bool $is_important = null;

	#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
	#[Groups(['task'])]
	#[SerializedName('completedOn')]
	private ?DateTimeInterface $completed_on = null;

	#[ORM\ManyToMany(targetEntity: Label::class, mappedBy: 'task')]
	#[Groups(['task'])]
	private Collection $labels;

	#[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'task')]
	#[Groups(['task'])]
	private Collection $assignee;

	public function __construct()
	{
		$this->labels = new ArrayCollection();
		$this->assignee = new ArrayCollection();
	}

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

	public function isIsCompleted(): ?bool
	{
		return $this->is_completed;
	}

	public function getTaskListId(): ?int
	{
		return $this->task_list_id;
	}

	public function setTaskListId(int $task_list_id): static
	{
		$this->task_list_id = $task_list_id;

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

	public function getStartOn(): ?DateTimeInterface
	{
		return $this->start_on;
	}

	public function setStartOn(?DateTimeInterface $start_on): static
	{
		$this->start_on = $start_on;

		return $this;
	}

	public function getDueOn(): ?DateTimeInterface
	{
		return $this->due_on;
	}

	public function setDueOn(?DateTimeInterface $due_on): static
	{
		$this->due_on = $due_on;

		return $this;
	}

	public function isIsImportant(): ?bool
	{
		return $this->is_important;
	}

	public function getCompletedOn(): ?DateTimeInterface
	{
		return $this->completed_on;
	}

	public function setCompletedOn(DateTimeInterface|null $completed_on): static
	{
		$this->completed_on = $completed_on;

		return $this;
	}

	public function setTaskWithParameters(mixed $parameters): Task
	{
		$this->setName($parameters['name']);
		$this->setIsCompleted($parameters['isCompleted']);
		$this->setTaskListId($parameters['taskListId']);
		$this->setPosition($parameters['position']);
		$this->setStartOn(date_create($parameters['startOn']));
		$this->setDueOn(date_create($parameters['dueOn']));
		$this->setIsImportant($parameters['isImportant']);

		return $this;
	}

	public function setIsCompleted(bool $is_completed): static
	{
		if ($is_completed) {
			$this->setCompletedOn(date_create());
		} else {
			$this->setCompletedOn(null);
		}
		$this->is_completed = $is_completed;

		return $this;
	}

	public function setIsImportant(bool $is_important): static
	{
		$this->is_important = $is_important;

		return $this;
	}

	/**
	 * @return Collection<int, Label>
	 */
	public function getLabels(): Collection
	{
		return $this->labels;
	}

	public function addLabel(Label $label): static
	{
		if (!$this->labels->contains($label)) {
			$this->labels->add($label);
			$label->addTask($this);
		}

		return $this;
	}

	public function removeLabel(Label $label): static
	{
		if ($this->labels->removeElement($label)) {
			$label->removeTask($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, User>
	 */
	public function getAssignee(): Collection
	{
		return $this->assignee;
	}

	public function addAssignee(User $assignee): static
	{
		if (!$this->assignee->contains($assignee)) {
			$this->assignee->add($assignee);
			$assignee->addTask($this);
		}

		return $this;
	}

	public function removeAssignee(User $assignee): static
	{
		if ($this->assignee->removeElement($assignee)) {
			$assignee->removeTask($this);
		}

		return $this;
	}
}
