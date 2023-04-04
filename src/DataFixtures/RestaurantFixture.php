<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RestaurantFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $restaurant = new Restaurant();
        $address = new Address();

        $restaurant->setName('Le Petit Bistrot');
        $restaurant->setDirector('Jean Money');

        $address->setCity('Paris');
        $address->setZip(75003);
        $address->setName('Rue de la Poisse');

        foreach ($restaurant->getMenus() as $menu) {
            $menu->setRestaurantId($restaurant);
        }

        foreach ($restaurant->getPlats() as $plat) {
            $plat->setRestaurantId($restaurant);
        }

        $restaurant->setAddressId($address);

        $manager->persist($address);
        $manager->persist($restaurant);
        $manager->flush();
    }
}
