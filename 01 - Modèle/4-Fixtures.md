# Utiliser des données factices

## Introduction

Une fois le modèle du domaine de l'application établi, il est souvent utile de pouvoir travailler sur un jeu de données de manière à pouvoir écrire des requêtes, construire des vues, rôder des formulaires, créer des classes de test, etc. Autrement dit, constituer un jeu de données, même temporaire, est une étape importante du cycle de développement. Cela étant dit, écrire ces données manuellement est un travail long, fastidieux, et qui de surcroît peut devoir être recommencé plusieurs fois (e.g. en cas de modification du modèle du domaine).

Pour cette raison, il existe une bibliothèque de Doctrine qui permet d'engendrer automatiquement autant de données que nécessaire en respectant le modèle, il s'agit de **DataFixtures**.

> **N.B.** : Si vous utilisez la version “_skeleton_” de Symfony et que vous devez installer la bibliothèque, il est recommandé de l'installer dans l'environnement de développement avec l'option `--dev` :
```bash
 composer require orm-fixtures --dev
```

## La bibliothèque DataFixtures

### Une classe de données factices

L'utilisation de Datafixtures est très simple puisqu'il suffit de créer des classes dans un sous-dossier `Datafixtures` du dossier applicatif `src`.

Cette classe s'écrit _a minima_ ainsi :
```php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{ /* ... */ }
```
Cette classe hérite de la class `Fixture` de Doctrine. Elle est très simple puisqu'elle n'exige qu'une seule méthode : `load`. Celle-ci dépend juste de l'`ObjectManager` de Doctrine :
```php
public function load(ObjectManager $manager);
```

> **N.B.** `ObjectManager` est la classe qui s'occupe pour Doctrine du contrôle des entités.  

La classe peut ainsi être réécrite de la manière suivante :
```php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load (ObjectManager $om)
    {
        /* ... */
    }
}
```
Les classes de données factices peuvent être facilement créées par la commande :
```bash
symfony console make:fixtures
```
La dépendance est ajoutée directement par Symfony grâce à l'**injection de dépendances**.

Le corps de la fonction `load` peut maintenant créer des entités d'une certane classe, assigner aux propriétés des valeurs arbitraires et, grâce à l'`ObjectManager` pérenniser (_persist_) et enregistrer dans la base les entités nouvelles.

### Lancer la création

Une fois les classes créées, il suffit de lancer le processus avec la commande de la console :
```bash
symfony console doctrine:fixtures:load
```

> **N.B.** : Par défaut (il existe d'autres options) le lancement de la commande efface toutes les données antérieurement contenues dans la base de données.

### Variantes dans le processus

#### Dépendances entre entités

Dans la plupart des cas, le modèle du domaine comprend des associations entre entités et, pour la commodité du processus, il sera souhaitable d'ordonner la création des entités. Par exemple, si un `Author` écrit plusieurs `Book`, il serait préférable de créer d'abord des livres puis de les associer aux auteurs.

Dans ce cas, nous pouvons recourir à la méthode `getDependencies`, fournie par l'interface `Doctrine\Common\DataFixtures\DependentFixtureInterface`.
```php
// dans la classe AuthorFixtures
public function getDependencies()
{
    return [
        BookFixtures::class
    ]
}
```
La méthode `load` de `AuthorFixtures` aura la garantie que des entités `Book` existent et que l'on peut les lier à des `Author`.

#### Groupes de fixtures

Dans un certain nombre de cas, nous voudrons seulement créer des données sur une sous-partie du domaine. Dans ce cas, il est possible de définir des groupes et de circonscrire le processus aux entités d'un groupe.

Pour cela `Datafixtures` dispose d'une méthode statique `getGroups` fournie par l'interface `Doctrine\Bundle\FixturesBundle\FixtureGroupInterface`.
```php
public static function getGroups() : array
{
    return [
        'groupDocuments',
        'groupUsers'
    ]
}

Il est maintenant possible de lancer le processus avec un groupe en option (voire plusieurs) :

```bash
symfony console doctrine:fixtures:load --group=groupDocuments
```

## Enrichir la description et la pertinence des données

`DataFixtures` fait l'essentiel du travail. Malheureusement, la seule méthode disponible est de créer des valeurs aléatoirement ou bien d'écrire des fonctions qui produiront des valeurs selon nos souhaits. Mais heureusement, des développeurs se sont déjà penchés sur la question et des outils existent pour faire exactement cela.

### Faker

La bibliothèque utilisée maintenant est `FakerPHP`, qui est la continuation de` Faker`, de François Zaninotto, mantenant abandonnée. Pour l'installer, il suffit d'exécuter la commande :
```bash
composer require fakerphp/faker --dev
```

`FakerPHP` fournit des “_Provider_” dont le rôle est de créer différents types de données, en fonction de paramètres particuliers (le pays, par exemple, si l'on cherche des villes ou des numéros de téléphone).

La manière la plus simple d'utiliser `FakerPHP` (dans Symfony) est en complément de `DataFixtures`. Par exemple :
```php
namespace App\DataFixtures;

// import de la fabrique (factory) de Faker pour engendrer les valeurs de différents Provider
use Faker\Factory as FakerFactory

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load (ObjectManager $om)
    {
        // En passant par la _fabrique_, l'injection de dépendance ne peut pas être utilisée
        $faker = FakerFactory::create();
        $doc = new Book();
        // La date de publicaton est contrainte à être entre hier et ily a cent jours
        $doc->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
        /* ... */
    }
}
```

La [Fabrique](https://www.wikiwand.com/fr/Fabrique_(patron_de_conception)) (« factory » en anglais) admet un paramètre de langue, nous permettant ainsi de récupérer des données “localisées” :
```php
$faker = FakerFactory::create('en_US');
```
Toutes les données ne sont pas concernées ; le nombre et le type des données localisées dépend fortement des langues. Le rôle de cette fabrique est d'instancier un objet de la classe `Faker\Generator` (rien à voiravec les générateurs de PHP) qui fera concrètement le travail.

Comme vous pourrez le constater, les noms des propriétés ne sont jamais ambiguës. C'est ce qui permet au `Generator` de reconnaître les propriétés (comme `setPublishedAt`) dans l'exemple précédent.

Je vous renvoie à la documentation de `Faker` pour expérimenter toutes les options des `Provider` et de leurs différentes propriétés.

Si une donnée vous manque (ou une langue), il et très simple de créer son propre `Provider`. Il s'agit d'une simple classe PHP qui doit hériter de la classe `Faker\Base` ou une de ses sous-classes. Il faut juste implémenter les méthodes qui produiront les “bonnes” données. Pour que cette classe soit comprise par `Faker`, vous pourrez l'ajouter au `Generator` :

```php
$faker = FakerFactory::create('fr_FR');
// Noter au passage que les _Provider_ implémentent le design pattern `Visiteur`
$faker->addProvider(new App\Faker\Provider\fr_FR\MyProvider($faker))
```
Chaque classe doit naturellement se référer à une langue particulière.

On pourra par exemple créer une classe pour les immatriculations de voitures françaises :
```php
namespace App\Faker\Provider\fr_FR;

use Faker\Provider\Base;

class Immatriculation extends Base
{
    public function carImmatriculation()
    {
        return /* ... */;
    }
}
```


### ZenstruckFoundry

`FakerPHP` est l'outil de base. D'autres bibliothèques l'utilisent en ajoutant leurs propres fonctionnalités. Une de celles qui a pris beaucoup d'importance est `Foundry`, qui se fonde aussi sur un mécanisme de « _factory_ », mais appliqué aux entités.

Pour installer `Foundry` depuis Composer :
```bash
composer require zenstruck/foundry --dev
```

#### Utiliser une fabrique

Tout d'abord, il faut créer un Fabrique associée à une entité, par la ligne de commande :
```bash
symfony console make:factory
```

Si votre entité est la classe `App\Entity\Book`, alors une classe `App\Factory\BookFactory`est créée.

Cette nouvelle classe permet de créer des objets factices par le biais de plusieurs méthodes, comme `createOne()`, `createMany()` ou `new()`. Ces méthodes s'appuient elles-mêmes sur la déclaration qui est faite dans la classe, au sein de la méthode `getDefaults()`. Cette méthode engendre les données relatives aux propriétés de l'objet créé. Par exemple, pour un livre, cela pourrait être :
```php
protected function getDefaults(): array
{
    return [
        /*
         * Foundry utilise FakerPHP pour engendrer des valeurs aléatoires.
         * Les clefs du tableaux correspondent aux nomdes propriétés de l'entité.
         */
        'title' => self::faker()->unique()->sentence(),
        'body' => self::faker()->sentence(),
    ];
}
```

Créer un objet se fait tout simplement avec l'instruction :
```php
// exécute silencieusement la méthode `getDefaults()` pour engendrer les valeurs
BookFactory::createOne();

// Les valeurs par défaut peuvent être surchargées
BookFactory::createOne(['title' => 'Le rouge et le noir']);
```
Il est même tout à fait possible d'en créer autant que nous voulons automatiquement :
```php
// Création de plusieurs livres en uneseule fois
BookFactory::createMany(20);
```

Les objets sont automatiquement « _pérennisés_ » (_persisted_) par `Foundry`, ce qui permet de les réutiliser facilement, notamment lorsque nous voudrons créer des associations entre entités (comme `Livre <- écrit par / a écrit -> Auteur`, par exemple).

`Foundry`sait retrouver tout seul les objets pérennisés, sans passer par des requêtes. Par exemple :
```php
// trouve tous les objets de la classe Book
BookFactory::all();
// trouve l'objet avec l'id 5 (comme Doctrine
BookFactory::find(5);
// trouve les objets répondant à la contrainte (les livres parus en 1982)
PostFactory::find(['parution' => '1982']);
// trouve un livre au hasard
PostFactory::random();
```
Il existe plusieurs manières de créer des objets :

```php
// crée et pérennise l'objet
BookFactory::createOne(['title' => 'Robinson Crusoë']);
// ne fait que créer l'objet,
// qui devra donc être pérennisé ensuite avec la méthode `create()
// souvnet utilisé avec des états, cf. ci-dessous
BookFactory::new()->create();
// crée et pérennise le nombred'objets que vous passez en arguemnt
BookFactory::createMany(20);
// prend un objet existant au hasard ou en crée un s'il n'en existe pas
BoofFactory::randomOrCreate()
```

> **N.B.** Contrairement à `FakerPHP`, il n'est nul besoin d'exécuter `flush()` pour écrire les données dans la base lorsque nous lancerons la commande `make:fixtures:load`. Cela est fait implicitement.

#### Etats

`Foundry` offre la possibilité de modulariser la création des objets par le biais d'états. Un « **état** » est juste une méthode de la fabrique qui engendre une donnée et l'attache à l'objet cia la méthode `addState`.
```php
// App\Factory\BookFactory

public function published(): self
{
    return $this->addState(['published_at' => self::faker()->dateTime()]);
}

public function unpublished(): self
{
    return $this->addState(['published_at' => null]);
}

```
Le processus de création est désormais un peu différent :
```php
// L'objet et initialisé avec la méthode `new`
// A ce stade il n'est ni créé ni enregistré
// Il est possible de passer à `new` des valeurs pour surcharger la méthode `getDefaults`.
$book = BookFactory::new(['title' => 'Tchevengour']);

// A ce livre,on associe l'état “non publié” (brouillon)
$book->unpublished()

// Le livre est maintenant créé et pérennisé par Doctrine
$book->create()
```

#### Attributs

Les attributs sont un autre moyen de surcharger les valeurs par défaut :
```php
$book = BookFactory::new(['title' => 'Tchevengour']);

// Attribut sous forme de valeur scalaire
$book->withAttribute(['pages' => 253])
// Attribut sous forme de fermeture
$book->withAttribute(function () use ($x) { return [
    'createdAt' => self::faker()->dateTime("-{$x} weeks")
    ]; }
)

// Le livre est maintenant créé et pérennisé par Doctrine
$book->create()
```

#### Les relations

`Foundry` permet de gérer simplement les associations entre entités.

Soit l'entité `Book`, associée à l'entité `Category` de telle sorte qu'un livre ne soit que dans une seule catégorie. Dans ce cas, il suffit d'appeler la fabrique correspondante :
```php
protected function getDefaults(): array
{
    return [
        'title' => self::faker()->unique()->sentence(),
        'body' => self::faker()->sentence(),
        'category' => CategoryFactory::random()
    ];
}
```
La méthode `getDefaults` étant exécutée à chaque fois que nous créons un livre, nous somme certains qu'une catégorie _différente_ sera choise aléatoirement pour chaque livre.

> **N.B.** La question des associations doit être examinée avec précaution.
> 
> Le code suivant :
> ```php
> Book::createMany(10, ['category' => CategoryFactory::random()])
>```
> 
> ne fonctionnera pas comme attendu. Tous les livres auronat la même catégorie, choisie une fois par la fabrique.
> 
> Pour être sûr d'avoir une catégorie pour chaque livre, il faut :
> - soit introduire une fonction anonyme :
> ```php
> // La Closure est exécutée  pour chaque nouvel objet
> Book::createMany(10, function () { return ['category' => CategoryFactory::random()]; })
>```
> - soit glisser l'attribut dans la méthode `getDefaults()`, comme un peu plus haut. 


## Ressources

* [DataFixtures dans la documentation de Symfony](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
* [Le code source de DataFixtures](https://github.com/doctrine/data-fixtures)
* [FakerPHP — Anfreas Möller](https://github.com/FakerPHP)
* [ZenstruckFoundry dans la documentation de Symfony](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html)