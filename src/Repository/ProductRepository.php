<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */
    public function findByNameCat($name, $category, $orderBy, $limit, $offset)
    {
        if (is_null($name)) {
            $name = '%';
        }
        if (is_null($category)) {
            $category = '%';
        }
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesk = $arr[1];
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name','%'.$name.'%')
            ->andWhere('p.category LIKE :category')
            ->setParameter('category', $category)
            ->orderBy('p.'.$order, $ascDesk)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
