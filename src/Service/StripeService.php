<?php

namespace App\Service;

use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createProduct($name): Product
    {
        return $this->stripe->products->create([
            'name' => $name,
        ]);
    }

    public function createPrice($amount, $productStripeId, $currency = 'usd'): Price
    {
        return $this->stripe->prices->create([
            'unit_amount' => (int)$amount * 100,
            'currency' => $currency,
            'product' => $productStripeId,
        ]);
    }

    public function updateProductName($stripeProductId, $name) {
        $this->stripe->products->update(
            $stripeProductId,
            ['name' => $name],
        );
    }

    public function archivePrice($stripeProductId) {
        $this->stripe->prices->update(
            $stripeProductId,
            ['active' => false],
        );
    }

    public function archiveProduct($stripeProductId) {
        $this->stripe->products->update(
            $stripeProductId,
            ['active' => false],
        );
    }
}