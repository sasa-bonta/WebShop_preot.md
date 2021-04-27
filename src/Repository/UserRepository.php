<?php

namespace App\Repository;

use App\Entity\User;
use App\SearchCriteria;
use App\UserSearchCriteria;
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

    public function search(UserSearchCriteria $searchCriteria)
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('u');
        if ($searchCriteria->getParam() !== null) {
            $query = $query
                ->where('u.username LIKE :param OR  u.email LIKE :param')
                ->setParameter('param', '%' . $searchCriteria->getParam() . '%');
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
    public function countTotal(UserSearchCriteria $searchCriteria)
    {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.id)');
        if ($searchCriteria->getParam() !== null) {
            $query = $query
                ->where('u.username LIKE :param OR  u.email LIKE :param')
                ->setParameter('param', '%' . $searchCriteria->getParam() . '%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
