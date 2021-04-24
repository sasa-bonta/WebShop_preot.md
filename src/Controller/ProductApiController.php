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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 * @method Exception(string $string)
 */
class ProductApiController extends AbstractController
{
    /**
     * @Route("/", name="productApi_index", methods={"GET"})
     * @throws Exception
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $name = $request->query->get('name');
        $category = $request->query->get('category');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if ($limit > 100) {
            return $this->json(array('code' => 400, 'message' => 'limit must be < 100>'));
        }
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (InvalidPageException $e) {
            return $this->json(array('code' => 400, 'message' => 'page must be positive'));
        } catch (InvalidLimitException $e) {
            return $this->json(array('code' => 400, 'message' => 'limit must be positive'));
        } catch (NonexistentOrderByColumn $e) {
            return $this->json(array('code' => 400, 'message' => 'nonexistent column name'));
        } catch (NonexistentOrderingType $e) {
            return $this->json(array('code' => 400, 'message' => 'nonexistent sort order'));
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit)) {
            return $this->json(array('code' => 400, 'message' => 'page limit exceeded'));
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/new", name="productApi_new", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $product = new Product();
        $entityManager = $this->getDoctrine()->getManager();
        $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
        $product->setCode($parameters['code']);
        $product->setName($parameters['name']);
        $product->setCategory($parameters['category']);
        $product->setPrice($parameters['price']);
        $product->setImgPath($parameters['imgPath']);
        $product->setDescription($parameters['description']);
        $product->setCreatedAt($dateTime);
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['status' => '201 Created',
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice(),
            'image' => $product->getImgPath(),
            'description' => $product->getDescription(),
            'created at' => $product->getCreatedAt()]);
    }

    /**
     * @Route("/{code}", name="productApi_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{code}", name="productApi_edit", methods={"PUT"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
//        var_dump($parameters);
        $product = $productRepository->findOneBy(array('code' => $parameters['code']));
        $entityManager = $this->getDoctrine()->getManager();
        $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
        $product->setName($parameters['name']);
        $product->setCategory($parameters['category']);
        $product->setPrice($parameters['price']);
        $product->setImgPath($parameters['imgPath']);
        $product->setDescription($parameters['description']);
        $product->setUpdatedAt($dateTime);
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['status' => '202 Updated',
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'category' => $product->getCategory(),
            'price' => $product->getPrice(),
            'image' => $product->getImgPath(),
            'description' => $product->getDescription(),
            'created at' => $product->getCreatedAt(),
            'updated at' => $product->getUpdatedAt()]);
    }

    /**
     * @Route("/{code}", name="productApi_delete", methods={"DELETE"})
     */
    public function delete(Product $product, ProductRepository $productRepository): JsonResponse
    {
        $productRepository->delete($product);
        return new JsonResponse(['status' => 'Product #' . $product->getCode() . ' deleted']);
    }
}