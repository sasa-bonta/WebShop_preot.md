<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('sasa');
        $user->setEmail('abonta@pentalog.com');
        $user->setPassword($this->encoder->encodePassword($user,'sasa'));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setApiToken('api-token');
        $manager->persist($user);
        $manager->flush();
    }
}