<?php


namespace App\Service;


use App\Repository\ProductRepository;

class CartService
{
//    private $productRepository;
//
//    public function __construct(ProductRepository $productRepository)
//    {
//        $this->productRepository = $productRepository;
//    }
//
//    public function add(string $productCode)
//    {
//
//        if (!empty($cart[$productCode])) {
//            $cart[$productCode]++;
//        } else {
//            $cart[$productCode] = 1;
//        }
//
//        $this->session->set('cart', $cart);
//    }
//
//    public function remove(string $productCode)
//    {
//        $cart = $this->session->get('cart', []);
//
//        if (!empty($cart[$productCode])) unset($cart[$productCode]);
//
//        $this->session->set('cart', $cart);
//    }
//
//    public function getFullCart(): array
//    {
//        $cart = $this->session->get('cart', []);
//
//        $cartWithData = [];
//
//        foreach ($cart as $productCode => $quantity) {
//            $cartWithData[] = [
//                'product' => $this->productRepository->findOneBy(['code' => $productCode]),
//                'quantity' => $quantity
//            ];
//        }
//
//        return $cartWithData;
//    }
//
//    public function getTotal(): float
//    {
//        $total = 0;
//
//        foreach ($this->getFullCart() as $item) {
//            $total += $item['product']->getPrice() * $item['quantity'];
//        }
//
//        return $total;
//    }


}