<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['task', 'user'])]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(['user'])]
	private ?string $name = null;

	#[ORM\Column(length: 255)]
	#[Groups(['user'])]
	#[SerializedName('avatarUrl')]
	private ?string $avatar_url = null;

	/** @Ignore() */
	#[ORM\Column(length: 255, unique: true)]
	private ?string $username = null;

	/** @Ignore() */
	#[ORM\Column(length: 255)]
	private ?string $password = null;

	/** @Ignore() */
	#[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'assignee')]
	private Collection $task;

	public function __construct()
	{
		$this->task = new ArrayCollection();
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

	public function getAvatarUrl(): ?string
	{
		return $this->avatar_url;
	}

	public function setAvatarUrl(string $avatar_url): static
	{
		$this->avatar_url = $avatar_url;

		return $this;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setUsername(string $username): static
	{
		$this->username = $username;

		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	public function getUserIdentifier(): string
	{
		return (string)$this->username;
	}

	public function getRoles(): array
	{
		return ['ROLE_USER'];
	}


	public function eraseCredentials(): void
	{
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
