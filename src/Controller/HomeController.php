<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{
    /**
     * @param Plat $plat
     * @param RestaurantRepository $restaurantRepository
     * @return Response
     */

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/home/{plat}', name: 'app_home')]
    public function index(Plat $plat, RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();
        $plats = ['burger', 'pizza', 'glace'];

        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants,
            'plats' => $plats
        ]);
    }


    #[Route('/test', name: 'app_test')]
    public function test(EntityManagerInterface $entityManager, RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = new Restaurant();
        $adresse = new Address();
        $adresse->setName('12 rue Lafayette');
        $adresse->setCity('Paris');
        $adresse->setZip(75009);
        $restaurants->setName('Super nom');
        $restaurants->setDirector('Michel');
        $restaurants->setAddress($adresse);

        $entityManager->persist($adresse);
        $entityManager->persist($restaurants);
        $entityManager->flush();

        $restaurants = $restaurantRepository->findAll();
        $adresse = $restaurantRepository->findAll();

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
            'adresse' => $adresse
        ]);
    }
}
