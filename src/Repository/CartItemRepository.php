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
        $this->cartItems = $this->findAll();
    }

    /**
     * @return CartItem[]
     */
    public function findItemsByUserId($userId): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c.code, c.amount')
            ->where('c.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
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

                $this->createQueryBuilder('c')
                    ->update()
                    ->set('c.amount', $amount)
                    ->where('c.code = :code')
                    ->setParameter('code', $productCode)
                    ->getQuery()
                    ->execute();
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

    public function deleteProduct(Product $product)
    {
        $this
            ->createQueryBuilder('c')
            ->delete()
            ->where('c.code = :code')
            ->setParameter('code', $product->getCode())
            ->getQuery()
            ->execute();
    }
}