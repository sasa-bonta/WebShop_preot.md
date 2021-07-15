<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use App\SearchCriteria\ProductAdminSearchCriteria;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_main", methods={"GET"})
     */
    public function main(): Response
    {
        return $this->redirectToRoute('product_list');
    }

    /**
     * @Route("/products/", name="product_list", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        try {
            $searchCriteria = new ProductAdminSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($searchCriteria->getPage() > ceil($length / $searchCriteria->getLimit()) && $searchCriteria->getPage() > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        return $this->render('admin/product/list_of_products.html.twig', [
            'products' => $productRepository->search($searchCriteria),
            'categories' => $productRepository->getCategories(),
            'length' => $length,
            'limit' => $searchCriteria->getLimit()
        ]);
    }

    /**
     * @Route("/products/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProductRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0) {
                $this->addFlash('code', "This code already exists");
            }

            return $this->render('admin/product/new.html.twig', [
                'product' => $product,
                'form' => $form->createView(),
            ]);

        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0) {
                $this->addFlash('code', "This code already exists");

                return $this->render('admin/product/new.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                ]);
            }

            $product->writeImgPathsFromArray($product->readImgPathsArray());
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/{code}", name="product_show_detailed", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        $product->writeImgPathEgal($product->readImgPathCSV());
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/products/{code}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $product->writeImgPathEgal($product->readImgPathCSV());
        $form = $this->createForm(ProductType::class, $product);
        $origCode = $product->getCode();
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(Product::class);
        if ($form->isSubmitted() && !$form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0 && $form->get('code')->getData() !== $origCode) {
                $this->addFlash('code', "This code already exists");

                return $this->render('admin/product/edit.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                ]);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->count(['code' => $product->getCode()]) > 0 && $form->get('code')->getData() !== $origCode) {
                $this->addFlash('code', "This code already exists");

                return $this->render('admin/product/edit.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                ]);
            }

            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($dateTime);
            $entityManager->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),]);
    }

    /**
     * @Route("/products/{code}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getCode(), $request->request->get('_token'))) {
            $cartItemRepository->deleteProduct($product);
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('product_list');
    }
}