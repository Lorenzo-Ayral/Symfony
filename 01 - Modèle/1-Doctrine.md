# Doctrine et les ORM, une introduction

## Introduction

La **P**rogrammation **O**rientée **O**bjet a introduit des problèmes spécifiques quant à l'hétérogénéité des représentations des données manipulées par les applications.

Un des problèmes principaux étant la conversion des données d'un format en graphe (les objets) en un format tabulaire (les SGBDR).

Sont alors apparus des outils dédiés à cette tâche, que l'on a appelés **O**bjet-**R**elational **M**apper (ORM). Ces outils viennent du monde **Java**, c'est-à-dire de grosses applications industrielles « _data intensive_ », comme disent les anglophones. Le plus connu d'entre eux est **Hibernate**, qui a beaucoup servi de modèle à Doctrine.

La fonction principale d'un ORM est de rendre les objets de l'application **persistants**, c'est-à-dire de maintenir la cohérence des données entre l'application et le support de stockage.

Pour répondre à ce besoin, les ORM introduisent une couche d'abstraction sur les données qui leur permet d'être (relativement) indépendants du stockage physique. Doctrine, par exemple, peut aussi bien gérer des bases de données relationnelles comme MySQL que des bases orientées document (NoSQL) comme MongoDB.

Du point de vue de l'application, il n'existe donc plus que des objets. Cela procure plusieurs avantages :
- Uniformité de la représentation des données
- Souplesse de changement du support de stockage
- Transparence des requêtes

Un ORM est responsable d'un certain nombre de tâches :
- la conversion des différents formats de données entre l'application et le système de stockage des données ;
- le maintien de la cohérence des données entre l'application et la base de données, via le « moteur de persistence » ;
- la gestion de la connexion au système de stockage des données, via des adaptateurs spécifiques ;
- les transactions (requêtes) avec les bases de données, avec une syntaxe spécifique :
- la vérification que les données sont valides, du point de vue de l'application comme de la base de données.

En général, les ORM offrent d'autres fonctionnalités, comme :
- engendrer des données factices d'après un modèle (pour des besoins de développement ou de tests)
- versionner le schéma de la base de données (relationnelle)
- mettre à disposition un ensemble d'événements à activer lors des transactions
- des extensions, dans le cas de Doctrine :
  - des ajouts à la syntaxe SQL (obtenir des objets partiels, par exemple)
  - des ajouts à la capacité de représentation de la POO (décrire des propriétés multilingues ou datées automatiquement)
