<?php

namespace App\DataFixtures;

use App\Entity\Auction;
use App\Entity\DownloadFiles;
use App\Entity\Offer;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    /**
     * @var UserPasswordHasherInterface
     * 
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /** 
     * constructor fixture
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $publicUser = new User();
        $publicUser->setUsername("admin");
        $publicUser->setRoles(["ROLE_ADMIN"]);
        $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, "admin"));
        $manager->persist($publicUser);

        $user[] =  $publicUser;

        $publicUser = new User();
        $publicUser->setUsername("public");
        $publicUser->setRoles(["ROLE_USER"]);
        $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, "public"));
        $manager->persist($publicUser);

        $user[] =  $publicUser;

        for ($i = 0; $i < 100; $i++) {
            $password = $this->faker->password;

            $publicUser = new User();
            $publicUser->setUsername($this->faker->name . "@" . $password);
            $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, $password));
            $publicUser->setRoles(["ROLE_USER"]);
            $manager->persist($publicUser);
            $user[] = $publicUser;
        }


        $auctionList = [];

        for ($i = 0; $i < 1000; $i++) {
            $auction = new Auction();
            $auction->setItemName($this->faker->name);
            $auction->setUser($this->faker->randomElement($user));
            $auction->setItemDescription($this->faker->text);
            $auction->setMinBid($this->faker->numberBetween(100, 1000));
            $auction->setPrice($this->faker->numberBetween(1000, 10000));

            $auction->setStartDate(new DateTimeImmutable());

            $auction->setCreatedAt(new DateTimeImmutable());

            $auction->setUpdatedAt(new DateTimeImmutable());

            $auction->setEndDate(new DateTimeImmutable("+100 days"));

            $auction->setStatus($this->faker->randomElement(['ACTIVE', 'DELETE']));
            $auctionList[] = $auction;
            $manager->persist($auction);
        }

        // $imageList = [];

        // for ($i = 0; $i < 1000; $i++) {
        //     $image = new DownloadFiles();
        //     $image->setAuction($this->faker->randomElement($auctionList));
        //     $image->setFile();
        //     $imageList[] = $image;
        //     $manager->persist($image);
        // }
        //     $manager->flush();


        for ($i = 0; $i < 1000; $i++) {
            $offer = new Offer();
            $offer->setAmount($this->faker->numberBetween(100, 1000));
            $offer->setUser($this->faker->randomElement($user));
            $offer->setCreatedAt(new DateTimeImmutable());
            $offer->setAuction($this->faker->randomElement($auctionList));
            $manager->persist($offer);
        }
        $manager->flush();
    }
}
