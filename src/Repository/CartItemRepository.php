<?php

namespace App\Repository;

use App\Entity\CartItem;
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

    public function add(string $productCode, int $userId, array $product) : bool
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
}