<?php


namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/cart")
 */
class CartApiController extends AbstractController
{
    /**
     * @Route("/", name="cart_api_index")
     */
    public function index(CartItemRepository $cartItemRepository, ProductRepository $productRepository)
    {
        $cart = $cartItemRepository->findItemsByUserId($this->getUserId());
        $cartDetailed = [];
        foreach ($cart as $cartItem) {
            $cartDetailed [] = [
                'product' => $productRepository->findAllByCodes($cartItem['code']),
                'amount' => $cartItem['amount']
            ];
        }

        return $this->json($cartDetailed);
    }

    /**
     * @Route("/{productCode}", name="cart_api_add", methods={"POST"})
     */
    public function addItem(Request $request, $productCode, CartItemRepository $cartItemRepository): JsonResponse
    {
        $response = new JsonResponse();

        $cartItemRepository->add($productCode, $this->getUserId());

        $data = ["status" => 200, "description" => "ok", "message" => "item added to cart"];
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/{code}", name="cart_api_delete", methods={"DELETE"})
     */
    public function deleteItem(CartItem $cartItem, CartItemRepository $cartItemRepository): JsonResponse
    {
        $response = new JsonResponse();

        $cartItemRepository->delete($cartItem);
        return $response;
    }

//    public function addAmount() {
//
//    }

//    public function decreaseAmount() {
//
//    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->get('security.token_storage')->getToken()->getUser()->getId();
    }
}