<?php


namespace App\Controller;


//use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index() {

        return $this->render("main/product/cart.html.twig");
    }
//
//    /**
//     * @Route("/cart/add/{productCode}", name="cart_add")
//     */
//    public function add($productCode, CartService $cartService) {
//        $cartService->add($productCode);
//
//        return $this->redirectToRoute("cart_index");
//    }
//
//    /**
//     * @Route("/cart/remove/{productCode}", name="cart_remove")
//     */
//    public function remove(string $productCode, CartService $cartService) {
//        $cartService->remove($productCode);
//
//        return $this->redirectToRoute('cart_index');
//    }
}