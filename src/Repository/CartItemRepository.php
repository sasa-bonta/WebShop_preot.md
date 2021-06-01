<?php

namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    private array $cartItems;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);

        $this->cartItems = $this->getEntityManager()
            ->createQuery(
                'SELECT c
                FROM App\Entity\CartItem c'
            )->getResult();
    }

    /**
     * @return CartItem[]
     */
    public function findItemsByUserId($userId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c.code, c.amount
            FROM App\Entity\CartItem c
            WHERE c.userId = :userId'
        )->setParameter('userId', $userId);

        return $query->getResult();
    }

    public function add(string $productCode, int $userId, array $product): bool
    {
        $entityManager = $this->getEntityManager();

        $item = [];
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getUserId() === $userId) {
                $item[] = [
                    'code' => $cartItem->getCode(),
                    'amount' => $cartItem->getAmount(),
                    'userId' => $cartItem->getUserId(),
                ];
            }
        }

        foreach ($item as $i) {
            if ($productCode === $i['code']) {
                $amount = $i['amount'] + 1;

                if ($amount > $product[0]['availableAmount']) return 0;

                $query = $entityManager->createQuery(
                    'UPDATE App\Entity\CartItem c
                    SET c.amount = :amount
                    WHERE c.code = :code'
                )->setParameter('amount', $amount)
                    ->setParameter('code', $productCode)
                    ->getResult();
                return 1;
            }
        }
        $cartItem = new CartItem();
        $cartItem->setCode($productCode);
        $cartItem->setAmount(1);
        $cartItem->setUserId($userId);

        if ($cartItem->getAmount() > $product[0]['availableAmount']) return 0;

        $entityManager->persist($cartItem);

        $entityManager->flush();
        return 1;
    }

    public function delete(CartItem $cartItem)
    {
        $this->getEntityManager()->remove($cartItem);
        $this->getEntityManager()->flush();
    }

    public function addOneItem(string $productCode, int $userId, array $product): bool
    {
        $entityManager = $this->getEntityManager();

        $item = [];
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getUserId() === $userId) {
                $item[] = [
                    'code' => $cartItem->getCode(),
                    'amount' => $cartItem->getAmount(),
                    'userId' => $cartItem->getUserId(),
                ];
            }
        }

        foreach ($item as $i) {
            if ($productCode === $i['code']) {
                $amount = $i['amount'] + 1;

                if ($amount > $product[0]['availableAmount']) return 0;

                $query = $entityManager->createQuery(
                    'UPDATE App\Entity\CartItem c
                    SET c.amount = :amount
                    WHERE c.code = :code'
                )->setParameter('amount', $amount)
                    ->setParameter('code', $productCode)
                    ->getResult();
                return 1;
            }
        }

        return 0;
    }

    public function deleteOneItem(string $productCode, int $userId, array $product): bool
    {
        $entityManager = $this->getEntityManager();

        $item = [];
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getUserId() === $userId) {
                $item[] = [
                    'code' => $cartItem->getCode(),
                    'amount' => $cartItem->getAmount(),
                    'userId' => $cartItem->getUserId(),
                ];
            }
        }

        foreach ($item as $i) {
            if ($productCode === $i['code']) {
                $amount = $i['amount'] - 1;

                if ($amount === 0) return 0;

                $query = $entityManager->createQuery(
                    'UPDATE App\Entity\CartItem c
                    SET c.amount = :amount
                    WHERE c.code = :code'
                )->setParameter('amount', $amount)
                    ->setParameter('code', $productCode)
                    ->getResult();
                return 1;
            }
        }

        return 0;
    }

    public function introduce($newAmount, string $productCode, int $userId, array $product) : int
    {
        $entityManager = $this->getEntityManager();

        $item = [];
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getUserId() === $userId) {
                $item[] = [
                    'code' => $cartItem->getCode(),
                    'amount' => $cartItem->getAmount(),
                    'userId' => $cartItem->getUserId(),
                ];
            }
        }

        foreach ($item as $i) {
            if ($productCode === $i['code']) {
                $amount = $newAmount;

                if ($amount <= 0) return 0;
                elseif ($amount > $product[0]['availableAmount']) return -1;

                $query = $entityManager->createQuery(
                    'UPDATE App\Entity\CartItem c
                    SET c.amount = :amount
                    WHERE c.code = :code'
                )->setParameter('amount', $amount)
                    ->setParameter('code', $productCode)
                    ->getResult();
                return 1;
            }
        }

        return 0;
    }

//    /**
//     * @return CartItem[] Returns an array of CartItem objects
//     */
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
    public function findOneBySomeField($value): ?CartItem
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