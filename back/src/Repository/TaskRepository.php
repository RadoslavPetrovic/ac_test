<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Task::class);
	}

	/**
	 * @return Task[] Returns an array of Task objects
	 */
	public function findByTaskListId($taskListId): array
	{
		return $this->createQueryBuilder('t')
			->andWhere('t.task_list_id = :val')
			->setParameter('val', $taskListId)
			->orderBy('t.id', 'ASC')
			->getQuery()
			->getResult();
	}
}
