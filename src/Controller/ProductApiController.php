<?php


namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\SearchCriteria;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/products")
 * @method Exception(string $string)
 */
class ProductApiController extends AbstractController
{
    /**
     * @Route("/", name="product_api_index", defaults={"_format":"json"}, methods={"GET"})
     * @throws Exception
     */
    public function index(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $category = $request->query->get('category');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit) && $page > 1) {
            throw new BadRequestHttpException("page limit exceeded");
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/new", name="product_api_new", defaults={"_format":"json"}, methods={"POST"})
     */
    public function new(Request $request, ProductRepository $repo): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($parameters);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->count(['code' => $parameters['code']]) > 0) {
                throw new BadRequestHttpException("this code already exists");
            } else {
                $data = ["status" => 201, "description" => "created", "message" => "new product is created"];
            }

            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            $response->setStatusCode(JsonResponse::HTTP_CREATED);
            $response->setData($data);
            return $response;
        } else {
            throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());
        }
    }

    /**
     * @Route("/{code}", name="product_api_show", defaults={"_format":"json"},methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{code}", name="product_api_edit", defaults={"_format":"json"}, methods={"PUT"})
     */
    public function edit(Request $request, Product $product, ProductRepository $repo): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);
        $initCode = $product->getCode();

        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($parameters);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0 && $product->getCode() !== $initCode) {
              throw new BadRequestHttpException("this code already exists");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            $data = ["status" => 200, "description" => "ok", "message" => "the product is updated"];
            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData($data);
            return $response;
        } else {
            throw new BadRequestHttpException($form->getErrors(true, true));
        }
    }

    /**
     * @Route("/{code}", name="product_api_delete", defaults={"_format":"json"}, methods={"DELETE"})
     */
    public function delete(Product $product, ProductRepository $productRepository): JsonResponse
    {
        $response = new JsonResponse();
        $productRepository->delete($product);
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $data = ["status" => 200, "description" => "ok", "message" => "the product is deleted"];
        $response->setData($data);
        return $response;
    }
}