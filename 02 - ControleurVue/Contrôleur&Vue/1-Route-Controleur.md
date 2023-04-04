## **Route & Controller**

Ressource :book: https://symfony.com/doc/current/page_creation.html#creating-a-page-route-and-controller

## *Controller*
>A controller is the PHP function you write that builds the page. You take the incoming request information and use it to create a Symfony Response object, which can hold HTML content, a JSON string or even a binary file like an image or PDF.  


## *Route*
>A route is the URL (e.g. /about) to your page and points to a controller;


## **Votre premier contrôleur**
Un contrôleur est représenté par un fichier PHP dans votre dossier src/controller.

Example d'un contrôleur qui renvoie une simple string.
```php
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route('/hello', name: 'hello')]
    public function hello(): Response
    {
        return new Response(
            'Hello'
        );
    }
}
```
* Un contrôleur Symfony est une classe qui hérite d'AbstractController.
* La classe fera partie du namespace App\Controller.
* L'objet Response permet de retourner une réponse HTTP, dans ce cas là, une simple string.


Dans la pluspart des cas, un contrôleur a pour vocation d'être relié à une Route pour être exécuté.
Grâce aux annotations, on peut relier la méthode d'un controller à une route directement comme ci-dessous :
```php
  #[Route('/hello', name: 'nom_route_unique'), methods: ['GET'])]
  public function methodController(){
     //....
  }
```
Les paramètres principaux sont :
1. L'URL
2. (optionnel) Un nom unique pour votre route
3. (optionnel) Les méthodes HTTP acceptées comme point d'entrée, sous forme d'un tableau de string (GET, POST, PUT, DELETE...)
   
Pour executer la méthode Hello du contrôleur HelloController, il faudra taper dans votre navigateur l'url : 
http://localhost:8888/hello


## **Tips** :bulb:

Vous pouvez créer vos contrôleurs rapidement depuis la console :
```bash
php bin/console make:controller
```

## **Tips :bulb:**
Vous pouvez créer une route pour l'ensemble de votre Controller.
Exemple ci-dessous :
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 #[Route('/order', name: 'order')]
class CheckoutController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
     //....
    }
    #[Route('/canceled', name: 'canceled')]
    public function payment(): Response
    {
     //....
    }
}
```
La route principale 'order' du Controller a été déclarée au dessus de la classe.
Pour accéder à la méthode payment ou canceled, il faudra taper l'url /order/payment ou payment/canceled.



## **À savoir :nerd_face:**
Il existe une autre manière de déclarer vos routes.
Dans le fichier routes.yaml, vous pouvez ajouter des routes comme ci-dessous :
```yaml
##/config/routes.yaml
nom_route:
    path: /url/example
    controller: App\Controller\NomControler::nomMethode
```
Bien que cette technique semble plus pratique à première vue  car il permet d'organiser toutes les routes dans un seul fichier, elle devient bien plus fastidieuse à mesure que votre projet grossit.
Cette méthode est devenue dépréciée avec l'arrivée des annotations.


