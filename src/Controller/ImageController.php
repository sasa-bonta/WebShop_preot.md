<?php

namespace App\Controller;

use App\Entity\Image;
use App\SearchCriteria\ImageSearchCriteria;
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

    private function uploadImageWithSecureName($form, $slugger): string
    {
        $imageFile = $form->get('path')->getData();
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
        // Move the file to the directory where brochures are stored
        try {
            $gallery = $this->getParameter('gallery_path');
            $imageFile->move(
                $gallery,
                $newFilename
            );
        } catch (FileException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        // updates the 'brochureFilename' property to store the PDF file name
        // instead of its contents
        return $newFilename;
    }

    private function checkTags(Image $image): array
    {
        $errors = [];
        foreach ($image->getTagsArray() as $tag) {
            if (mb_strlen($tag) > 12 || mb_strlen($tag) < 2) {
                $errors['tagLen'] = "The length of each tag must be from 2 to 12 characters";
            }
            if (preg_match('/[^a-z??-??0-9]/', $tag)) {
                $errors['tagMatch'] = "The tags must contain only characters and digits";
            }
        }
        return $errors;
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
            $errors = $this->checkTags($image);

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
            $image->setPath($this->uploadImageWithSecureName($form, $slugger));
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
                $image->setPath($this->uploadImageWithSecureName($form, $slugger));
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('image_index');
        }

        return $this->render('admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    public function deleteImageFromProducts(Image $image, ProductRepository $productRepository)
    {
        $products = $productRepository->findByImage($image);
        foreach ($products as $product) {
            $paths = $product->readImgPathsArray();
            array_splice($paths, array_search($image->getPath(), $paths), 1);
            (count($paths) === 0) ? $product->writeImgPathsFromArray(["no-image.png"]) : $product->writeImgPathsFromArray($paths);
            $date = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($date);
            $productRepository->updateImgPath($product);
        }
    }

    /**
     * @Route("/{id}", name="image_delete", methods={"POST"})
     */
    public function delete(Request $request, Image $image, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->deleteImageFromProducts($image, $productRepository);
            $filesystem = new Filesystem();
            $gallery = $this->getParameter('gallery_path');
            $filesystem->remove($gallery . $image->getPath());
            $entityManager->remove($image);
            $entityManager->flush();
        }
        return $this->redirectToRoute('image_index');
    }
}
