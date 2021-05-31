<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Product;
use App\SearchCriteria\SearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
    public function search(SearchCriteria $searchCriteria): array
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
     * @throws NonUniqueResultException
     * @throws NoResultException
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
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function delete(Product $product)
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }

    /**
     */
    public function findAllByCodes(string $code)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p.code, p.name, p.imgPath, p.price, p.availableAmount')
            ->where('p.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();
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

    public function findByImage(Image $image): array
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.imgPath LIKE :imgPath')
            ->setParameter('imgPath', '%"' .$image->getPath() .'"%')
            ->getQuery()
            ->getResult();
    }

    public function updateImgPath(Product $product) {
        $this
            ->createQueryBuilder('p')
            ->update()
            ->set('p.imgPath', json_encode($product->getImgPath()))
            ->set('p.updated_at', $product->getUpdatedAt()->format('Y-m-d H:i:s'))
            ->where('p.id = :id')
            ->setParameter('id', $product->getId())
            ->getQuery();
    }
}
