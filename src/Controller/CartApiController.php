<?php


namespace App\Controller;

use App\Repository\CartItemRepository;
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
    public function index(CartItemRepository $cartItemRepository)
    {
        return $this->json($cartItemRepository->findItemsByUserId($this->getUserId()));
    }

    /**
     * @Route("/{productCode}", name="cart_api_add")
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
     * @Route("/{code}", name="cart_api_delete", methods={"DELETE")
     */
//    public function deleteItem()
//    {
//
//    }

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