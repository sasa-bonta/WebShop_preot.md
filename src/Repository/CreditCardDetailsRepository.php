<?php

namespace App\Repository;

use App\Entity\CreditCardDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreditCardDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditCardDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditCardDetails[]    findAll()
 * @method CreditCardDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditCardDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditCardDetails::class);
    }

    // /**
    //  * @return CreditCardDetails[] Returns an array of CreditCardDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreditCardDetails
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
