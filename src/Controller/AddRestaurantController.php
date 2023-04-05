<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\AddRestaurantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddRestaurantController extends AbstractController
{
    #[Route('/add/restaurant', name: 'app_add_restaurant')]
    public function index(Request $request, $entityManager): Response
    {
        $restaurant = new Restaurant;

        $form = $this->createForm(AddRestaurantType::class, $restaurant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            // Redirect to the home page or to the newly created restaurant page
          /*  return $this->redirectToRoute('app_home');*/
        }

        return $this->renderForm('add_restaurant/index.html.twig', [
            'AddRestaurant' => $form,
        ]);
    }
}

