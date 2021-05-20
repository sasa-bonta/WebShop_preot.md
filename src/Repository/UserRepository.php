<?php

namespace App\Repository;

use App\Entity\User;
use App\SearchCriteria\SearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function search(SearchCriteria $searchCriteria)
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('u');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('u.username LIKE :param OR  u.email LIKE :param')
                ->setParameter('param', '%' . $searchCriteria->getName() . '%');
        }
        return $query
            ->orderBy('u.' . $searchCriteria->getOrder(), $searchCriteria->getAscDesc())
            ->setFirstResult($offset)
            ->setMaxResults($searchCriteria->getLimit())
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countTotal(SearchCriteria $searchCriteria)
    {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.id)');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('u.username LIKE :param OR  u.email LIKE :param')
                ->setParameter('param', '%' . $searchCriteria->getName() . '%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }
}
