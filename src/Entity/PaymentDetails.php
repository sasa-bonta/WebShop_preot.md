<?php

namespace App\Entity;

use App\Repository\PaymentDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class PaymentDetails
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Embedded(class="CreditCardDetails")
     */
    private $card;

    public function __construct()
    {
        $this->card = new CreditCardDetails();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
