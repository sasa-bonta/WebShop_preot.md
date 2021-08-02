<?php

namespace App\Service;

use App\Entity\Image;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    private ProductRepository $productRepository;
    private SluggerInterface $slugger;
    private string $galleryPath;

    public function __construct(ProductRepository $repo, SluggerInterface $slugger, $galleryPath)
    {
        $this->productRepository = $repo;
        $this->slugger = $slugger;
        $this->galleryPath = $galleryPath;
    }

    public function deleteImageFromProducts(Image $image)
    {
        $products = $this->productRepository->findByImage($image);
        foreach ($products as $product) {
            $paths = $product->readImgPathsArray();
            array_splice($paths, array_search($image->getPath(), $paths), 1);
            (count($paths) === 0) ? $product->writeImgPathsFromArray(["no-image.png"]) : $product->writeImgPathsFromArray($paths);
            $date = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($date);
            $this->productRepository->updateImgPath($product);
        }
    }

    public function uploadImageWithSecureName($form): string
    {
        $imageFile = $form->get('path')->getData();
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
        // Move the file to the directory where brochures are stored
        try {
            $gallery = $this->galleryPath;
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

    public function checkTags(Image $image): array
    {
        $errors = [];
        foreach ($image->getTagsArray() as $tag) {
            if (mb_strlen($tag) > 12 || mb_strlen($tag) < 2) {
                $errors['tagLen'] = "The length of each tag must be from 2 to 12 characters";
            }
            if (preg_match('/[^a-zа-я0-9]/', $tag)) {
                $errors['tagMatch'] = "The tags must contain only characters and digits";
            }
        }
        return $errors;
    }
}