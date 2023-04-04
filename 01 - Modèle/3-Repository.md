# Les requêtes sur le modèle avec Doctrine

## Introduction

Les requêtes sont une partie assez complexe de Doctrine, d'une part parce qu'il existe plusieurs syntaxes alternatives ; d'autre part parce qu'i ly a un risquede confusion entre la syntaxe de base de Doctrine (DQL) et SQL. Bien qu'étant très proches, ce sont en fait deux choses très différentes qui n'offrent pas les mêmes “services”.

Doctrine permet d'utiliser quatre syntaxes différentes pour construire les requêtes :
1. **QueryBuilder**,qui est la syntaxe « de haut niveau », basée sur des fonctions ;
2. **DQL**, qui est un langage proche de SQL et qui adopte les même format textuel ;
3. **Native Query Builder**, qui est une syntaxe permettant de dépasser les limitations de DQL (au prix d'une certaine complexité) ;
4. **pseudo-PDO**, il est en effet aussi possible d'écrire des requêtes « de type PDO ».

> **N.B.** Dans cette partie nous n'aborderons que la quatrième partie (écrire du SQL), qui ne sera utilisée que de manière temporaire jusqu'au cours sur DQL.

## Les classes de requêtes

### Rôle des classes de requêtes

Lorsque nous crééons des entités (cf Chapitre ...), Symfony engendre non seulement la classe de l'entité mais aussi une **classes de requêtes** appelée `Repository`. Son nom est d'ailleurs identifiable puisqu'il contient le nom de l'entité à laquelle la classe se réfère. Nous verrons que cela a une certaine importance.

Par défaut, toutes les requêtes doivent être écrites dans des classes de requêtes. Vous trouverez fréquemment des exemples ou des tutoriels dans lesquels le requêtes  (c'est aussi le cas ^pour les formulaires) sont écrites directement dans les contrôleurs. Ceci est une mauvaise pratique. En effet, le contrôleur ne fait rien par lui-même (sauf exception, naturellement :).

Les classes de requêtes héritent de `Doctrine\ORM\EntityRepository` ce qui leu octroit des capacités natives, comme le faire de savoir exécuter des requêtes « basiques » :
* `find` : trouver une entité par son `id` ;
* `findAll` :  touver toutes les entités (généralment déconseillé :) ;
* `findBy` et `findOneBy` : trouver une ou des entités selon la valeur de propriétés ;
* `findBy<property>` : variante de la précédente ou le nom d'une propriété est intégrée dans la méthode.

### Exemples

```php
namespace App\Controller

class ArtistController
{
    public function getArtist(ArtistRepository $rep, int $id)
    {
      // Notez que cette syntaxe a tendance à être de plus en plus remplacée par l'emploi de `ParamConverter`
      $artist = $rep->find($id);
      /* ... */
    }

    public function getAllArtists(ArtistRepository $rep)
    {
      // $artist est un objet du type `ArrayCollection`
      $artist = $rep->findAll();
      /* ... */
    }

    public function getOneArtistWithName(ArtistRepository $rep, string $name)
    {
      // $artist est un objet du type `Artist`
      // Les deux appels retournent la première entité disponible
      // 1) Utilisation de findOneBy
      $artist = $rep->findOneBy('name', $name);
      /* ... */
      // 2) Utilisation de findOneByName
      // Doctrine interprète automatiquement toutes ces méthodes spécifiques comprenant le nom des proriétés de l'entité
      $artist = $rep->findOneByName($name);
      /* ... */
    }

    public function getArtistsWithName(ArtistRepository $rep, string $name)
    {
      // $artist est un objet du type `ArrayCollection`
      // 1) Utilisation de findBy
      $artist = $rep->findBy('name', $name);
      /* ... */
      // 2) Utilisation de findByName
      // Doctrine interprète automatiquement toutes ces méthodes spécifiques comprenant le nom des proriétés de l'entité
      $artist = $rep->findByName($name);
      /* ... */
    }
}
```

### Créer une classe de requêtes

Comme nous l'avons vu, les classes de requêtes, associées aux entités, sont créées automatiquement grâce à la commande `make:entity` et hébergées dans lr dossier `src/Repository`. Globalement, cela ressemble à ça :
```php
namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtistRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::_construct($registry, Artist::class)
  }
}
```
Les classes de requêtes n'ont pas nativement accès à toutes les formes de requêtes que nous détaillerons dans les prochains mémentos, mais elles ont accès à l'`EntityManager`, car elles héritent de la méthode `getEntityManager()` de la classe `EntityRepository`. nous pourrons donc écrire un squelette de méthode  :

```php
public function findWorksFormArtist(int $id)
{
  $em = $this->getEntityManager();

  /* Code permettant d'écrire et d'exécuter la requête */
}
```

## Ecrire de simples requêtes textuelles

Pour les personnes habituées à utiliser PHP/PDO pour interagir avec une base de données relationnelle, il existe une possibilité assez simple et proche de vos habitudes.

Toutes les classes héritant de `EntityRepository` ont un accès à l'`EntityManager`, qui lui-même sait se connecter à la base de données. Il est donc possible d'écrire quelque chose comme :
```php
// Dans une méthode du Repository
$db = $this->getEntityManager()->getconnection();
```

Nous nous retrouvons alors dans un cas relativement bien connu, mais dont la syntaxe diffère : Contrairement à SQL, nous ne faisons jamais référence à des tables et des colonnes mais à des entités et des propriétés. C'est Doctrine qui se chargera de faire l'appariement entre entités et tables.

Dans le cas le plus simple, une requête s'écrira :
```sql
-- L'entité doit être identifiée par son nom qualifié
SELECT * FROM artist WHERE id < 10
```
Ce qui signifie que nous cherchons des entités `Artist` de notre application.

La suite est identique à ce que nous ferions avec PDO.
```php
// $sql contient le texte d'une requête
$results = $db->fetchAll($sql);
```

L'intérêt de cette méthode est double :
* La méthode d'écriture est proche de ce que l'on connaît
* `fetchAll` renvoie des **objets de la classe de l'entité**, ce que nous savions être déjà possible avec PDO, mais ici de manière plus simple et puissante.

Une méthode dans une classe de requêtes s'écrit ainsi :
```php
namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtistRepository extends ServiceEntityRepository
{
  /**
   * [https description]
   * @type {[type]}
   */
  public function findAliveArtists()
  {
    $db = $this->getEntityManager()->getconnection();
    // Le critère de sélection fait référence à une propriété de l'objet `a` et non à une colonne dans une table SQL
    $dql = 'SELECT * FROM App\Entity\Artist a WHERE a.deathDate IS NULL';
    $statement = $db->prepare($dql);
    $statement->execute();
    return $statement->fetchAll();
  }
}
```

Cette méthode pseudo-PDO est toutefois peu utilisée dans la pratique. On préférera passer par directement par l'`EntityManager` et le `QueryBuilder`.

### Bonnes Pratiques

Les requêtes Doctrine **DOIVENT** être implémentées dans une méthode d‘une classe de requêtes (Repository). Il est tout à fait possible d'écrire les requêtes _directement_ dans les classes de contrôleurs, mais ceci est considéré comme une mauvaise pratique, en vertu de la séparation des responsabilités.

## Ressources

[Requêtes sur le modèle dans Symfony](https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository)
