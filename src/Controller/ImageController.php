<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/galery")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/index.html.twig', [
            'images' => $imageRepository->findAll(),
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

            $tags = mb_strtolower($image->getTags());
            $errors = [];

            $tags = explode(',', $tags);
            $trimmedTags = [];
            foreach ($tags as $tag) {
                $tag = ltrim($tag);
                $tag = rtrim($tag);
                array_push($trimmedTags, $tag);
            }

            foreach ($trimmedTags as $tag) {
                if (mb_strlen($tag) > 12 || mb_strlen($tag) < 2) {
                    array_push($errors, "The length of each tag must be from 2 to 12 characters");
                }
                if (preg_match('/[^a-zа-я0-9]/', $tag)) {
                    array_push($errors, "The tags must contain only characters and digits");
                }
            }

            if (!empty($errors)) {
                return $this->render('admin/image/new.html.twig', [
                    'errors' => $errors,
                    'image' => $image,
                    'form' => $form->createView(),
                ]);
            }

            $imageFile = $form->get('path')->getData();
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
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
            $image->setTags(json_encode($trimmedTags));
            $image->setPath($newFilename);
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
    # @fixme edit image
    public function edit(Request $request, Image $image): Response
    {
//        $gallery = $this->getParameter('gallery_path');
//        $image->setPath($gallery .$image->getPath());
//        $image->setPath(new File($image->getPath()));
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_index');
    }
}
