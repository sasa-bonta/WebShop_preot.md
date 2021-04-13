<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
    public function search($name, $category, $orderBy, $limit, $offset)
    {
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesk = $arr[1];
        # добавь проверку $order и $ascDesc, потому что если поля не существует или $ascDesc будет
        # иметь другое значение ты получишь ошибку
        if ($order !== 'created_at' and $order !== 'price') {
            throw new Exception('Unregistered order by');
        }
        if ($ascDesk !== 'ASC' and $ascDesk !== 'DESC') {
            throw new Exception('Impossible sorting type');
        }
        $query = $this->createQueryBuilder('p');
        if ($name !== null) {
            $query = $query
                ->where('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%'); # на самом деле это должно быть name% вместо %name%
        }
        if ($category !== null) {
            $query = $query
                ->andWhere('p.category LIKE :category')
                ->setParameter('category', $category);
        }
        return $query
            ->orderBy('p.' . $order, $ascDesk)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function countTotalLength($name, $category)
    {
        if (is_null($name)) {
            $name = '%';
        }
        if (is_null($category)) {
            $category = '%';
        }
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->andWhere('p.category LIKE :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
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
