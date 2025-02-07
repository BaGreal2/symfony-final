<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setPrice(rand(100, 1000));
            $product->setCreatedAt(new \DateTime());
            $manager->persist($product);
        }

        $adminGroup = new Group();
        $adminGroup->setName('Admins');
        $adminGroup->setCreatedAt(new \DateTime());
        $manager->persist($adminGroup);
        $userGroup = new Group();
        $userGroup->setName('Users');
        $userGroup->setCreatedAt(new \DateTime());
        $manager->persist($userGroup);


        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setCreatedAt(new \DateTime());
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($hashedPassword);

        $user->addGroup($adminGroup);
        $manager->persist($user);

        $manager->flush();
    }
}
