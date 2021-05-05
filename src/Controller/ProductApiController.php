<?php


namespace App\Controller;

use App\Entity\Product;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderByColumn;
use App\Exceptions\NonexistentOrderingType;
use App\Repository\ProductRepository;
use App\SearchCriteria;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/products")
 * @method Exception(string $string)
 */
class ProductApiController extends AbstractController
{
    private function makeResponse400($message): JsonResponse
    {
        $response = new JsonResponse();
        $response->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
        $data = ["status" => 400, "description" => "bad request","message" => $message];
        $response->setData($data);
        return $response;
    }

    /**
     * @Route("/", name="productApi_index", methods={"GET"})
     * @throws Exception
     */
    public function index(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $category = $request->query->get('category');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if ($limit > 100) {
            return $this->makeResponse400("the limit must be <= 100");
        }
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (InvalidPageException $e) {
            return $this->makeResponse400("page must be positive");
        } catch (InvalidLimitException $e) {
            return $this->makeResponse400("limit must be positive");
        } catch (NonexistentOrderByColumn $e) {
            return $this->makeResponse400("nonexistent column name");
        } catch (NonexistentOrderingType $e) {
            return $this->makeResponse400("nonexistent sort order");
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit)) {
            return $this->makeResponse400("page limit exceeded");
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/new", name="productApi_new", defaults={"_format":"json"}, methods={"POST"})
     */
    public function new(Request $request, ProductRepository $repo): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);
        $product = new Product();
        $entityManager = $this->getDoctrine()->getManager();
        $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));

        if (empty($parameters['code']) or empty($parameters['name']) or empty($parameters['category']) or empty($parameters['price']) or empty($parameters['description'])) {
            return $this->makeResponse400("some parameter is missing");
        } elseif ($repo->count(['code' => $parameters['code']]) > 0) {
            return $this->makeResponse400("this code already exists");
        } elseif (!is_float($parameters['price'])){
            return $this->makeResponse400("the price is not float");
        } else {
            $data = ["status" => 201, "description" => "created","message" => "new product is created"];
        }

        if (empty($parameters['imgPath'])) {
            $product->setImgPath("/assets/main/images/no-image.png");
        } else {
            $product->setImgPath($parameters['imgPath']);
        }

        $product->setCode($parameters['code']);
        $product->setName($parameters['name']);
        $product->setCategory($parameters['category']);
        $product->setPrice($parameters['price']);
        $product->setDescription($parameters['description']);
        $product->setCreatedAt($dateTime);
        $entityManager->persist($product);
        $entityManager->flush();

        $response->setStatusCode(JsonResponse::HTTP_CREATED);
        $response->setData($data);
        return $response;
    }

    /**
     * @Route("/{code}", name="productApi_show", defaults={"_format":"json"},methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{code}", name="productApi_edit", defaults={"_format":"json"}, methods={"PUT"})
     */
    public function edit(Request $request, Product $product, ProductRepository $repo): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);

        if ($repo->findOneBy(array('code' => $parameters['code'])) === null) {
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
            $data = ["status" => 204, "description" => "no content","message" => "the product with this code doesn't exist"];
            $response->setData($data);
            return $response;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));

        if (empty($parameters['code']) or empty($parameters['name']) or empty($parameters['category']) or empty($parameters['price']) or empty($parameters['description'])) {
            return $this->makeResponse400("some parameter is missing");
        } elseif ($repo->count(['code' => $parameters['code']]) > 0 and $parameters['code'] !== $product->getCode()) {
            return $this->makeResponse400("this code already exists");
        } elseif (!is_float($parameters['price'])){
            return $this->makeResponse400("the price is not float");
        } else {
            $data = ["status" => 200, "description" => "ok","message" => "the product is updated"];
        }

        $product->setCode($parameters['code']);
        $product->setName($parameters['name']);
        $product->setCategory($parameters['category']);
        $product->setPrice($parameters['price']);
        $product->setImgPath($parameters['imgPath']);
        $product->setDescription($parameters['description']);
        $product->setUpdatedAt($dateTime);
        $entityManager->persist($product);
        $entityManager->flush();

        $response->setStatusCode(JsonResponse::HTTP_OK);
        $response->setData($data);
        return $response;
    }

    /**
     * @Route("/{code}", name="productApi_delete", defaults={"_format":"json"}, methods={"DELETE"})
     */
    public function delete(Product $product, ProductRepository $productRepository): JsonResponse
    {
        $productRepository->delete($product);
        $response->setStatusCode(JsonResponse::HTTP_OK);
        $data = ["status" => 200, "description" => "ok","message" => "the product is deleted"];
        $response->setData($data);
        return $response;
    }
}