# **Templates**

Ressource :book: https://symfony.com/doc/current/templates.html#twig-templating-language

>Les templates permettent d'afficher les données préparées par les contrôleurs.
La séparation de la récupération des données et de l'affichage des données est la base du modèle MVC ( Model, View, Controller) que vous connaissez déjà. 


## Template Engine, **Twig** :rocket:
Ressource :book:  https://twig.symfony.com/doc/3.x/

Nous pourrions structurer l'affichage des données nous mêmes dans des fichiers "phtml" par exemple, mais Symfony va nous simplifier la tâche grâce au puissant moteur de template dénommé Twig.

Ses intérêts sont rapidement compréhensibles :
* Twig nous permet d'éviter l'ouverture des balises PHP pour afficher des variables.

    Avant TWIG :
    ```html
    <h1> 
    <?php echo $titre ?>
    <?php echo $post->getTitle() ?>
    </h1>
    ```
    Avec TWIG :
    ```twig
    <h1>
    {{ titre }}
    {{ post.getTitle }}
    </h1>
    ```

* Twig embarque aussi des fonctionnalitées plus puissantes comme des conditions, des boucles, des filtres, et des fonctions.
    ```twig
    {% if today == "lundi" %}
        <h1>Bon courage</h1>
    {% endif %}

    <ul>
    {% for user in users %}
        <li> {{ user.name }} </li>
    {% else %}
        <p>No users have been found.</p>
    {% endfor %}
    </ul>
    ```

* Et aussi, un système d'héritage de templates et de blocks pour structurer nos pages et ne pas se répéter.

#

## **Rendre un template**

Depuis un contrôleur.
```php
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        
        $fruits = ["Banane", "Pomme", "Fraise"];


        return $this->render('home/index.html.twig', [
            "fruits" => $fruits
        ]);
    }

```
Il suffit d'appeler la méthode "render" qui prend en arguments :
1. le chemin du template voulu, 
2. un tableau associatif contenant l'ensemble des données (variables) que vous souhaitez transmettre au template.

À partir du moment où vous envoyez ses données au template, elles seront lisibles dans Twig grâce aux doubles moustaches :
```twig
{{ fruits[0] }}
```


#
## Blocks & Extends
Ressource :book:
 https://symfony.com/doc/current/templates.html#template-inheritance-and-layouts

La structure la plus commune est d'avoir un template de base contenant tout le html qui se qui se répète sur l'ensemble de vos pages.
( header, footer, liens CSS / JS...)

Dans cette base commune, vous pouvez créer des blocks pour le contenu qui sera propre à chaque page.

Example :
```twig
<!-- templates/base.html -->
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
        <title>
        {% block title %}
        Page Contact
        {% endblock %}
        </title>
        ...
    </head>
...
<body>
 {% block body %}
       Je suis la page d'accueil.
 {% endblock %}
 </body>

```
On peut voir que ce template contient le doctype, la balise head, le charset...
Un block title est créé au niveau de la balise title.

C'est notre template de base.


Maintenant, prenons une page finale comme la page contact.
La page contact sera un nouveau fichier dans le dossier templates qui héritera du template de base :
```twig
<!-- templates/contact.html -->
{% extends 'base.html.twig' %}

{% block title %}
Page Contact
{% endblock %}

```
**L'héritage nous permet de nous concentrer sur l'essentiel.**
Pas besoin de dupliquer sur chaque page le doctype par exemple.
On réécrit seulement les blocks que nous voulons remplacer.


# Les functions 
 ## Assets
 Les fichiers d' Images, CSS et JS sont à déposer dans le dossier "public".
 Pour lier ces fichiers à votre html, utilisez la fonction asset qui retourne le chemin absolu jusqu'au dossier **public** :
 ```twig
     <link href="{{asset('css/index.css')}}" rel="stylesheet">
     <img src="{{asset('images/piloupilou.jpg')}}">
     <script src="{{asset('js/script.js')}}"></script>
```
:warning: à ne pas confondre avec le dossier Asset que nous verrons plus tard avec webpack.
<br>
<br>
## **Path**
Permet d'afficher le chemin absolu vers une Route dans le cas d'une redirection par exemple.
```twig
{{ path('maRoute' }}
```

## **Include**

Il peut arriver que vous vouliez inclure des templates dans d'autres. C'est une bonne pratique, pour rendre plus lisible votre code.
Si votre template fait 500 lignes, c'est que vous pouvez le découper en sous ensemble.
```twig
{% include 'header.html' %}
```

## **Render**
Si vous souhaitez récupérer une information depuis un contrôleur, dans le cas d'une _API_ par exemple, vous pouvez executer la méthode d'un contrôleur depuis Twig comme ceci :
```twig
<!-- grâce à la route  -->
{{ render(path('latest_articles', {max: 3})) }}
<!--  ou directement avec le nom du controller et de la méthode -->
{{ render(controller(        'App\\Controller\\BlogController::recentArticles', {max: 3}))}}
```

## **Dump**
Si vous souhaitez vérifiez le contenu de vos variables :
```twig
{{ dump(user) }}
```

## Concaténation
Au besoin, vous pouvez concaténer grâce à la tilde :
```twig
{{ variable1 ~ variable2 }}
```

## Parent
```twig
{{ parent() }} 
```
 Ajoute dans le bloc le contenu du bloc parent (le comportement par défaut est le remplacement)


## Variables 
Vous pouvez créer des variables grâce à la fonction set.
```twig
{% set foo = 'bar' %}
{{ foo }}
```

# Les filtres
Vous pouvez utilisez des filtres sur vos variables pour y appliquer des fonctions.

Filtres les plus connus :
### date
```twig
{{ maVariableDate|date("m/d/Y") }}
```
### lower
### upper
### round
### length
<br/>


**Découvrez les autres fonctions et filtres :**
https://twig.symfony.com/doc/3.x/
#



## La variable globale `app`

| Variable | Explication | Renvoi |
|---|---|---|
| app.user | L'utilisateur (connecté) actuel | Sécurité |
| app.request | L'objet de la classe `Request` représentant le requête HTTP | Contrôleurs |
| app.session | L'objet de la classe `Session` contenant les données de la session courante | Contrôleurs |
| app.flashes | Un tableau contenant l'ensemble des « messages Flash » | Twig |
| app.environment | Le nom de l'environnement courant (dev, prod, etc) | Configuration |
| app.debug | Valeur booléenne en fonction de l'activation du mode debug | Configuration |
| app.token | L'objet de la classe `TokenInterface` contenant le jeton de sécurité CSRF | Sécurité |

