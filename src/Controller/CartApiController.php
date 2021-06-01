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
 * @Route("/api/v1/cart", defaults={"_format":"json"})
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
                'product' => $productRepository->findOneBy(['code' => $cartItem['code']]),
                'amount' => $cartItem['amount']
            ];
        }

        return $this->json($cartDetailed);
    }

    /**
     * @Route("/{productCode}", name="cart_api_add", methods={"GET","POST"})
     */
    public function addItem($productCode, CartItemRepository $cartItemRepository, ProductRepository $productRepository): JsonResponse
    {
        $response = new JsonResponse();

        if ($cartItemRepository->add($productCode, $this->getUserId(), (array)$productRepository->findAllByCodes($productCode))) {
            $data = ["status" => 200, "description" => "ok", "message" => "item added to cart"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        } else {
            $data = ["status" => 200, "description" => "ok", "message" => "item out of stock"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        }

        return $response;
    }

    /**
     * @Route("/{code}", name="cart_api_delete", methods={"DELETE"})
     */
    public function deleteItem(CartItem $cartItem, CartItemRepository $cartItemRepository): JsonResponse
    {
        $response = new JsonResponse();
        $cartItemRepository->delete($cartItem);
        $data = ["status" => 200, "description" => "ok", "message" => "item deleted"];
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/increase/{productCode}", name="cart_api_increase_amount", methods={"PATCH"})
     */
    public function increaseAmount($productCode, CartItemRepository $cartItemRepository, ProductRepository $productRepository): JsonResponse
    {
        $response = new JsonResponse();

        if ($cartItemRepository->addOneItem($productCode, $this->getUserId(), (array)$productRepository->findAllByCodes($productCode))) {
            $data = ["status" => 200, "description" => "ok", "message" => "amount increased"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        } else {
            $data = ["status" => 200, "description" => "ok", "message" => "item out of stock"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        }

        return $response;
    }

    /**
     * @Route("/decrease/{productCode}", name="cart_api_decrease_amount", methods={"PATCH"})
     */
    public function decreaseAmount($productCode, CartItemRepository $cartItemRepository, ProductRepository $productRepository): JsonResponse
    {
        $response = new JsonResponse();

        if ($cartItemRepository->deleteOneItem($productCode, $this->getUserId(), (array)$productRepository->findAllByCodes($productCode))) {
            $data = ["status" => 200, "description" => "ok", "message" => "amount decreased"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        } else {
            $data = ["status" => 200, "description" => "ok", "message" => "amount cannot be 0"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
        }

        return $response;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->get('security.token_storage')->getToken()->getUser()->getId();
    }
}