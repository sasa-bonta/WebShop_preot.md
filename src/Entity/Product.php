<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imgPath;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $availableAmount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailableAmount()
    {
        return $this->availableAmount;
    }

    /**
     * @param mixed $availableAmount
     */
    public function setAvailableAmount($availableAmount): void
    {
        $this->availableAmount = $availableAmount;
    }

    // get paths: json -> CSV
//    public function getImgPathCSV(): ?string
//    {
//        return str_replace(["[", "]", "\""], " ", $this->imgPath);
//    }

    // returns string|array|csv
    public function getImgPath() {
        return $this->imgPath;
    }

    // get paths: json -> array
//    public function getPathsArray(): ?array
//    {
//        return json_decode($this->imgPath);
//    }

    // set paths: "csv" -> array -> json
    public function setImgPath(string $paths): self
    {
        $paths = explode(',', $paths);
        $trimmedPaths = [];
        foreach ($paths as $path) {
            $path = ltrim($path);
            $path = rtrim($path);
            array_push($trimmedPaths, $path);
        }
        $this->imgPath = json_encode($trimmedPaths);
        return $this;
    }

    // set paths: string
    public function setImagePathEgal($paths): self
    {
        $this->imgPath = $paths;
        return $this;
    }

    // set paths: array -> json
    public function setPathsFromArray(array $paths): self
    {
        $this->imgPath = json_encode($paths);
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
