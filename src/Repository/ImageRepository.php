<?php

namespace App\Repository;

use App\Entity\Image;
use App\SearchCriteria\SearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function search(SearchCriteria $searchCriteria)
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('i');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('i.tags LIKE :param')
                ->setParameter('param', '%"' . $searchCriteria->getName() . '"%');
        }
        return $query
            ->orderBy('i.' . $searchCriteria->getOrder(), $searchCriteria->getAscDesc())
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
        $query = $this->createQueryBuilder('i')
            ->select('count(i.id)');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('i.tags LIKE :param')
                ->setParameter('param', '%"' . $searchCriteria->getName() . '"%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }
}
