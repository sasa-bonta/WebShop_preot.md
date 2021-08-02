<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Form\ImageEditType;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\SearchCriteria\ImageSearchCriteria;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// @Route("/api/v1/gallery", defaults={"_format":"json"})

/**
 * @Route("/api/v1/gallery")
 */
class ImageApiController extends AbstractController
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @Route("/", name="image_api_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository, Request $request): JsonResponse
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

        return $this->json($imageRepository->search($searchImage));
    }

    /**
     * @Route("/", name="image_api_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = new JsonResponse();
        $parameters = $request->request->all();
        $files = $request->files->all();
        $data = array_replace_recursive($parameters, $files);
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($data);
        // @fixme 02/08/2021 verify if all fields are !== null

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */

            // Check tags
            $errors = $this->imageService->checkTags($image);

            if (!empty($errors)) {
                throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());
            }

            // Add tags
            $image->setTagsFromArray($image->getTagsArray());
            // Add image
            $image->setPath($this->imageService->uploadImageWithSecureName($form));
            $entityManager->persist($image);
            $entityManager->flush();
            $data = ["status" => 201, "description" => "created", "message" => "new image added", "id" => $image->getId()];
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setData($data);
            return $response;
        }
        throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());
    }

    /**
     * @Route("/{id}", name="image_api_show", methods={"GET"})
     */
    public function show(Image $image): JsonResponse
    {
        return $this->json($image);
    }

    /**
     * @Route("/{id}", name="image_api_edit", methods={"POST"})
     */
    public function edit(Request $request, Image $image, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = new JsonResponse();
        $origPath = $image->getPath();
        $parameters = $request->request->all();
        $files = $request->files->all();
        $data = array_replace_recursive($parameters, $files);
        $form = $this->createForm(ImageEditType::class, $image, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $form->submit($data);
        // @fixme 02/08/2021 verify if all fields are !== null

        if ($form->isSubmitted() && $form->isValid()) {

            $errors = $this->imageService->checkTags($image);
            if (!empty($errors)) {
                throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());

            }

            if ($image->getPath() === '# % & { } \\ / $ ! \' \" : @ < > * ? + ` | =') {
                $image->setPath($origPath);
            } else {
                $image->setPath($this->imageService->uploadImageWithSecureName($form));
            }
            $entityManager->flush();
            $data = ["status" => 201, "description" => "created", "message" => "the image is updated"];
            $response->setStatusCode(Response::HTTP_OK);
            $response->setData($data);
            return $response;
        }
        throw new BadRequestHttpException($form->getErrors(true, true)->current()->getMessage());
    }

    /**
     * @Route("/{id}", name="image_api_delete", methods={"DELETE"})
     */
    public function delete(Image $image, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = new JsonResponse();
        $this->imageService->deleteImageFromProducts($image);
        $filesystem = new Filesystem();
        $gallery = $this->getParameter('gallery_path');
        $filesystem->remove($gallery . $image->getPath());
        $entityManager->remove($image);
        $entityManager->flush();
        $data = ["status" => 200, "description" => "ok", "message" => "the image is deleted"];
        $response->setData($data);
        return $response;
    }
}