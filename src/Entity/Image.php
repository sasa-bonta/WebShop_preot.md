<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    // returns tag1, tag2, tag3
    public function getTags(): ?string
    {
        return str_replace(["[", "]", "\""], " ", $this->tags);
    }

    // get tags: json -> array
    public function getTagsArray(): ?array
    {
        return json_decode($this->tags);
    }

    // set tags: "csv" -> array -> json
    public function setTags(string $tags): self
    {
        $tags = strtolower($tags);
        $tags = explode(',', $tags);
        $trimmedTags = [];
        foreach ($tags as $tag) {
            $tag = ltrim($tag);
            $tag = rtrim($tag);
            array_push($trimmedTags, $tag);
        }
        $this->tags = json_encode($trimmedTags);
        return $this;
    }

    public function setTagsFromArray(array $tags): self
    {
        $this->tags = json_encode($tags);
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
