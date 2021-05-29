<?php


namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\SearchCriteria\ProductAdminSearchCriteria;
use App\SearchCriteria\SearchCriteria;
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
 * @Route("/api/v1/products", defaults={"_format":"json"})
 * @method Exception(string $string)
 */
class ProductApiController extends AbstractController
{
    /**
     * @Route("/", name="product_api_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request): JsonResponse
    {
        try {
            $searchCriteria = new ProductAdminSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($searchCriteria->getPage() > ceil($length / $searchCriteria->getLimit()) && $searchCriteria->getPage()) {
            throw new BadRequestHttpException("page limit exceeded");
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/", name="product_api_new", methods={"POST"})
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

            $product->writeImgPathsFromArray($product->readImgPathsArray());
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
     * @Route("/{code}", name="product_api_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{code}", name="product_api_edit", methods={"PUT"})
     */
    public function edit(Request $request, Product $product, ProductRepository $repo): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);
        $initCode = $product->getCode();
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $keys = ['code', 'name', 'category', 'price', 'availableAmount', 'description'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $parameters) || empty($parameters[$key])) {
                throw new BadRequestHttpException($form->getErrors(true, true));
            }
        }
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
     * @Route("/{code}", name="product_api_delete", methods={"DELETE"})
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