<?php

namespace App\Repository;

use App\Entity\Order;
use App\SearchCriteria\OrderSearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countTotal(OrderSearchCriteria $searchCriteria)
    {
        // @fixme search by ????
        $query = $this->createQueryBuilder('o')
            ->select('count(o.id)');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('o.username LIKE :param OR  u.email LIKE :param')
                ->setParameter('param', '%' . $searchCriteria->getName() . '%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }
}
