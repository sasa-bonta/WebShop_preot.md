<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\SearchCriteria;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
            throw new BadRequestHttpException("400");
        }
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (Exception $e) {
            throw new BadRequestHttpException("400");
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit)) {
            throw new BadRequestHttpException("400");
        }

        return $this->json($productRepository->search($searchCriteria));
    }

    /**
     * @Route("/new", name="productApi_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Errors solve later
//            $repo = $this->getDoctrine()->getRepository(Product::class);
//            if ($repo->count(['code'=>$product->getCode()]) > 0) {
//                # code 400, display alert. Return this
//                return $this->render('product/new.html.twig', [
//                    'errors' => ['duplicated code'],
//                    'product' => $product,
//                    'form' => $form->createView(),
//                ]);
//            }
            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('main/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="productApi_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{code}/edit", name="productApi_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($dateTime);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('productApi/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="productApi_delete", methods={"DELETE"})
     */
    public function delete(Product $product, ProductRepository $productRepository): JsonResponse
    {
        $productRepository->delete($product);
        return new JsonResponse(['status' => 'Product #' .$product->getCode() .' deleted']);
    }
}