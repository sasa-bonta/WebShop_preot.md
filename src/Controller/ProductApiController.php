<?php


namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\SearchCriteria\ProductAdminSearchCriteria;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Stripe\StripeClient;
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
// defaults={"_format":"json"}

class ProductApiController extends AbstractController
{

    private StripeClient $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

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
        if ($searchCriteria->getPage() > ceil($length / $searchCriteria->getLimit()) && $searchCriteria->getPage() > 1) {
            throw new BadRequestHttpException("page limit exceeded");
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/", name="product_api_new", methods={"POST"})
     */
    public function new(Request $request, ProductRepository $repo, EntityManagerInterface $entityManager): JsonResponse
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

            $stripeProduct = $this->stripe->products->create([
                'name' => $product->getName(),
            ]);

            $stripePrice = $this->stripe->prices->create([
                'unit_amount' => (int)$product->getPrice() * 100,
                'currency' => 'usd',
                'product' => $stripeProduct->id,
            ]);

            $product->setStripeProductId($stripeProduct->id);
            $product->setStripePriceId($stripePrice->id);

            $product->writeImgPathsFromArray($product->readImgPathsArray());
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setData($data);
            return $response;
        } else {
            throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());
        }
    }

    /**
     * @Route("/stripe", name="stripe_import_products", methods={"GET"})
     */
    public function stripeProductsImport(ProductRepository $productRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $count = 0;
        $response = new JsonResponse();

        foreach ($productRepository->findAll() as $product) {
            if ($product->getStripeProductId() !== null) {
                continue;
            }

            $stripeProduct = $this->stripe->products->create([
                'name' => $product->getName(),
            ]);

            $stripePrice = $this->stripe->prices->create([
                'unit_amount' => (int)$product->getPrice() * 100,
                'currency' => 'usd',
                'product' => $stripeProduct->id,
            ]);

            $product->setStripeProductId($stripeProduct->id);
            $product->setStripePriceId($stripePrice->id);
            $entityManager->flush();
            $count++;
        }

        $data = ['status' => 200, 'products added to stripe' => $count];
        $response->setData($data);
        return $response;
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
    public function edit(Request $request, Product $product, ProductRepository $repo, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = json_decode($request->getContent(), true);
        $initProduct = clone $product;
        $form = $this->createForm(ProductType::class, $product, ['csrf_protection' => false]);
        $form->handleRequest($request);

        $formRequires = ['code', 'name', 'category', 'price', 'availableAmount', 'description'];
        foreach ($formRequires as $required) {
            if (!array_key_exists($required, $parameters) || is_null($parameters[$required]) || $parameters[$required] === "") {
                throw new BadRequestHttpException("The parameter " . $required . " is absent");
            }
        }

        $form->submit($parameters);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0 && $product->getCode() !== $initProduct->getCode()) {
                throw new BadRequestHttpException("this code already exists");
            }

            if ($product->getName() !== $initProduct->getName()) {
                $this->stripe->products->update(
                    $product->getStripeProductId(),
                    ['name' => $product->getName()],
                );
            }

            if ($product->getPrice() !== $initProduct->getPrice()) {
                $this->stripe->prices->update(
                    $product->getStripePriceId(),
                    ['active' => false],
                );
                $this->stripe->prices->create([
                    'unit_amount' => (int)$product->getPrice() * 100,
                    'currency' => 'usd',
                    'product' => $product->getStripeProductId(),
                ]);
            }

            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            $data = ["status" => 200, "description" => "ok", "message" => "the product is updated"];
            $response->setStatusCode(Response::HTTP_OK);
            $response->setData($data);
            return $response;
        } else {
            throw new BadRequestHttpException($form->getErrors(true, true));
        }
    }

    /**
     * @Route("/{code}", name="product_api_delete", methods={"DELETE"})
     */
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = new JsonResponse();
        $this->stripe->prices->update(
            $product->getStripePriceId(),
            ['active' => false],
        );
        $this->stripe->products->update(
            $product->getStripeProductId(),
            ['active' => false],
        );
        $entityManager->remove($product);
        $entityManager->flush();
        $response->setStatusCode(Response::HTTP_OK);
        $data = ["status" => 200, "description" => "ok", "message" => "the product is deleted"];
        $response->setData($data);
        return $response;
    }
}