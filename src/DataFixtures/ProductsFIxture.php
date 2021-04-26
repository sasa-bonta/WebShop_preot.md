<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFIxture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (['productCOde1', 'productCOde2'] as $code){
            $product = new Product();
            $product->setCode($code);
            $product->setName("Product $code");
            $categ = ['cars', 'toys', 'supplies', 'tools'];
            $product->setCategory($categ[rand(0,3)]);
            $product->setPrice(rand(1, 1601));
            $product->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ');
            $product->setImgPath('assets/main/images/product1.jpg');
            $product->setCreatedAt(new \DateTime());


            $manager->persist($product);

        }

        $manager->flush();
    }
}
