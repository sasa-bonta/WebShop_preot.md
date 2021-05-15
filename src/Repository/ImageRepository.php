<?php

namespace App\Repository;

use App\Entity\Image;
use App\ImageSearchCriteria;
use App\UserSearchCriteria;
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

    public function search(ImageSearchCriteria $searchCriteria)
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('i');
        if ($searchCriteria->getTag() !== null) {
            $query = $query
                ->where('i.tags LIKE :param')
                ->setParameter('param', '%"' . $searchCriteria->getTag() . '"%');
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
    public function countTotal(ImageSearchCriteria $searchCriteria)
    {
        $query = $this->createQueryBuilder('i')
            ->select('count(i.id)');
        if ($searchCriteria->getTag() !== null) {
            $query = $query
                ->where('i.tags LIKE :param')
                ->setParameter('param', '%"' . $searchCriteria->getTag() . '"%');
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Image[] Returns an array of Image objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
