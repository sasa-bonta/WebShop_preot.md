<?php

namespace App\Controller;

use App\Entity\Image;
use App\SearchCriteria\ImageSearchCriteria;
use App\Service\ImageService;
use DateTime;
use DateTimeZone;
use App\Form\ImageEditType;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/gallery")
 */
class ImageController extends AbstractController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @Route("/", name="image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository, Request $request): Response
    {
        try {
            $searchImage = new ImageSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $imageRepository->countTotal($searchImage);
        if ($searchImage->getPage() > ceil($length / $searchImage->getLimit()) && $searchImage->getPage() > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        return $this->render('admin/image/index.html.twig', [
            'images' => $imageRepository->search($searchImage),
            'length' => $length,
            'limit' => $searchImage->getLimit()
        ]);
    }

    /**
     * @Route("/fragment", name="gallery_fragment", methods={"GET"})
     */
    public function temp(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/gallery_fragment.html.twig', [
            'images' => $imageRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="image_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */

            // Check tags
            $errors = $this->imageService->checkTags($image);

            if (!empty($errors)) {
                return $this->render('admin/image/new.html.twig', [
                    'errors' => $errors,
                    'image' => $image,
                    'form' => $form->createView(),
                ]);
            }

            // Add tags
            $image->setTagsFromArray($image->getTagsArray());
            // Add image
            $image->setPath($this->imageService->uploadImageWithSecureName($form));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
            return $this->redirectToRoute('image_index');
        }

        return $this->render('admin/image/new.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_show", methods={"GET"})
     */
    public function show(Image $image): Response
    {
        return $this->render('admin/image/show.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Image $image, SluggerInterface $slugger): Response
    {
        $origPath = $image->getPath();
        $form = $this->createForm(ImageEditType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $errors = $this->checkTags($image);
            if (!empty($errors)) {
                return $this->render('admin/image/edit.html.twig', [
                    'errors' => $errors,
                    'image' => $image,
                    'form' => $form->createView(),
                ]);
            }
            // @fixme later with DTO and deserialization
            if ($image->getPath() === '# % & { } \\ / $ ! \' \" : @ < > * ? + ` | =') {
                $image->setPath($origPath);
            } else {
                $image->setPath($this->imageService->uploadImageWithSecureName($form));
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('image_index');
        }

        return $this->render('admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_delete", methods={"POST"})
     */
    public function delete(Request $request, Image $image, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->imageService->deleteImageFromProducts($image);
            $filesystem = new Filesystem();
            $gallery = $this->getParameter('gallery_path');
            $filesystem->remove($gallery . $image->getPath());
            $entityManager->remove($image);
            $entityManager->flush();
        }
        return $this->redirectToRoute('image_index');
    }
}
