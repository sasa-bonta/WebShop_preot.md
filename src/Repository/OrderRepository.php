<?php

namespace App\Repository;

use App\Entity\Order;
use App\SearchCriteria\OrderSearchCriteria;
use App\SearchCriteria\SearchCriteria;
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

    public function search(OrderSearchCriteria $searchCriteria): array
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('o');

        if ($searchCriteria->getId() !== null) {
            $query = $query
                ->where('o.id LIKE :id')
                ->setParameter('id', '%' . $searchCriteria->getId() . '%');
        }

        if ($searchCriteria->getStatus() !== null) {
            $query = $query
                ->andWhere('o.status LIKE :status')
                ->setParameter('status', $searchCriteria->getStatus());
        }

        return $query
            ->orderBy('o.' . $searchCriteria->getOrder(), $searchCriteria->getAscDesc())
            ->setFirstResult($offset)
            ->setMaxResults($searchCriteria->getLimit())
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countTotal(OrderSearchCriteria $searchCriteria)
    {
        $query = $this->createQueryBuilder('o')
            ->select('count(o.id)');
        if ($searchCriteria->getId() !== null) {
            $query = $query
                ->where('o.id LIKE :id')
                ->setParameter('id', '%' . $searchCriteria->getId() . '%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }
}
