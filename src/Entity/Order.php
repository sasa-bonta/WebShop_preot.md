<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $items = [];

    /**
     * @ORM\Column(type="object")
     */
    private $paymentDetails;

    /**
     * @ORM\Column(type="object")
     */
    private $address;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="array")
     */
    private $status = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails($paymentDetails): self
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }

    public function setStatus(array $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
