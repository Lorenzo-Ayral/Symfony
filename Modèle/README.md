# Les modèles de données

## Présentation

Ce module traite de la conception des **modèles de données** pour les applications. D'une manière générale, les modèles de données se basent sur une représentation de type [Entité/Association](1-Modèles.md) ou de ses dérivées, comme **UML**.

> **N.B.** On trouve aussi dans la littérature la dénomination « Modèle Conceptuel des Données », qui fait référence à la dimension abstraite de la représentation, indépendante de l'implémentation.

Ce module est la première étape de l'étude de **Doctrine**, qui est ce que l'on appelle un **ORM** (Object-Relational Mapper). C'est un outil central dans les applications Symfony puisque c'est lui qui est responsable de la gestion du **modèle** (cf. [Modèles et ORM](1-Modèles.md)). 

Nous parlerons progressivement de tous les aspects de Doctrine et la syntaxe des requêtes sur le modèle fera l'objet d'un prochain module.

Nous abordons donc ici l'architecture des **entités** qui sont le versant structural de l'ORM et comment ces entités, qui représentent des objets « métier », sont liés entre eux.

Symfony offrant les outils pour automatiser l'implémentation des différentes classes relatives aux entités, il est davantage question de conception d'un modèle juste (du point de vue du métier).

C'est pourquoi les exercices sont orientés vers la production de ce modèle, à partir de la spécification de l'application et de ses cas d'utilisation tels qu'ils auront été définis au début de la session.

Le volet fonctionnel de Doctrine sera abordé dans le module sur les formulaires et le CRUD.

## Notes de cours
- [Introduction Doctrine ORM](1-Doctrine.md)
- [Architecture des entités](2-Entités.md)
- [Requêtes simples avec `AbstractRepository`](3-Repository.md)
- [Travailler avec des données factices](4-Fixtures.md)

## Syntaxe
- [Aide-mémoire](Aide-mémoire.md)

## Ressources
- [Ressources documentaires](Ressources.md)
