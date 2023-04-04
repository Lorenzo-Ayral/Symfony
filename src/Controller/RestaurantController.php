<?php

namespace App\Controller;


use App\Repository\AddressRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    #[Route('/restaurant', name: 'app_restaurant')]
    public function index(RestaurantRepository $restaurantRepository, AddressRepository $addressRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();
        $address = $addressRepository->findAll();


        return $this->render('restaurant/index.html.twig', [
            'controller_name' => 'RestaurantController',
            'restaurants' => $restaurants,
            'address' => $address,
        ]);
    }
}
