<?php

namespace App\Entity;

use App\Repository\PaymentDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentDetailsRepository::class)
 */
class PaymentDetails
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currency;

    /**
     * @ORM\Embedded(class="CreditCardDetails", nullable=true)
     */
    private $card;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function setCard($card): void
    {
        $this->card = $card;
    }
}
