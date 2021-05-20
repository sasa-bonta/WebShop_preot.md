<?php

namespace App\Repository;

use App\Entity\Product;
use App\SearchCriteria\SearchCriteria;
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
    public function search(SearchCriteria $searchCriteria)
    {
        $offset = ($searchCriteria->getPage() - 1) * $searchCriteria->getLimit();

        $query = $this->createQueryBuilder('p');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('p.name LIKE :name')
                ->setParameter('name', '%' . $searchCriteria->getName() . '%');
        }
        if ($searchCriteria->getCategory() !== null) {
            $query = $query
                ->andWhere('p.category LIKE :category')
                ->setParameter('category', $searchCriteria->getCategory());
        }
        return $query
            ->orderBy('p.' . $searchCriteria->getOrder(), $searchCriteria->getAscDesc())
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
        $query = $this->createQueryBuilder('p')
            ->select('count(p.id)');
        if ($searchCriteria->getName() !== null) {
            $query = $query
                ->where('p.name LIKE :name')
                ->setParameter('name', '%' . $searchCriteria->getName() . '%');
        }
        if ($searchCriteria->getCategory() !== null) {
            $query = $query
                ->andWhere('p.category LIKE :category')
                ->setParameter('category', $searchCriteria->getCategory());
        }
        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Product $product)
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Product
     */
    public function findAllByCodes(string $code)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p.code, p.name, p.imgPath, p.price, p.availableAmount
            FROM App\Entity\Product p
            WHERE p.code = :code'
        )->setParameter('code', $code);

        return $query->getResult();
    }
  
    public function getCategories(): array
    {
        $categories = $this
            ->createQueryBuilder('p')
            ->select("DISTINCT p.category")
            ->getQuery()
            ->getResult();
        return array_column($categories, 'category');
    }
}
