# Le cycle des requêtes

## Présentation

Ce module aborde la question du cycle de traitement des requêtes par Symfony, souvent appelé — un peu simplement — **MVC** (pour « Modèle-Vue-Contrôleur).

Plusieurs composants permettent de fournir à l'utilisateur la ressource qui correspond à sa demande :

* le **routeur** dont le rôle est d'analyser la requête HTTP lue par le serveur HTTP (souvent Apache) et de construire les objets qui permettront aux autres composants de faire leur travail. L'objet produit qui nous intéressera en premier lieu est une instance de la classe `Request`. Le routeur apparaît dans l'application principalement sous forme de la description des routes, qui forment l'**API** de l'application. 

* le **contrôleur** qui est le superviseur des traitements. Un controleur est une méthode déclenchée par l'application après l'analyse du routeur et dont le rôle sera d'organiser les traitements nécessaires à la construction d'une **réponse** qui sera renvoyée à l'utilisateur. Pour cela, les contrôleurs font massivement appel au _modèle de données_ (cf. [M2](../M2-Modèle/README.md)). Les réponses sont des objets de la classe `Response` (ou une de ses alternatives).

Les réponses encapsulent la représentation de la ressource telle que demandée par l'utilisateur. Le plus souvent, cette réponse est une « page » au format `text/html`. Dans ce cas, il faudra faire appel à un **moteur de rendu** pour construire une **vue**. Pour Symfony, il s'agit de `Twig`. Le rôle du moteur de rendu est de fusionner des squelettes de pages et des données qui lui auront été fournies par le contrôleur. 

![MVC schéma](./pictures/mvc.png)


## Notes de cours
- [Routes & contrôleurs](1-Route-Controleur.md)
- [Twig](2-Templates.md)
- [Requêtes (Request)](3-Request.md)
- [Réponses](4-Response.md)

## Syntaxe
- [Aide-mémoire](Aide-mémoire.md)

## Ressources
- [Ressources documentaires](Ressources.md)
