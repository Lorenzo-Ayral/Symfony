# Les entités

## Introduction

Les entités décrivent le modèle des objets de notre application. Elles constituent le premier volet de la gestion du modèle par l'ORM (**O**bject **R**elational **M**apper ) Doctrine.

Une entité reprend donc les grandes lignes de la programmation objet, et y ajoute des caractéristiques spécifiques à l'échange des données entre l'application et la base de données. 

Dans la pratique ce sont juste des classes PHP dont le but est de permettre la manipulation des propriétés. Leur rôle est essentiellement déclaratif mais on peut y implémenter des méthodes en lien avec la manipulation et la transformation des propriétés de cette classe.

Ces caractéristiques sont insérées sous forme d'attributs (qui ont remplacé les annotations depuis PHP 8). Doctrine se servira en effet des attributs pour convertir les objets en leur équivalent relationnel et inversement.


```php
    // Exemple d'attribut de propriété d'entité
    #[ORM\Column(type="string", length=255)]
    private $title;
```
```php
     // Exemple d'annotation avant PHP 8
     /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;
```
> Les annotations sont liées à un formalisme appelé *PHPDOC* qui utilise les commentaires à des fins d'analyse statique du code. 
Depuis la version 8 de PHP, les attributs remplissent les même fonctions de manière plus efficace. 

Il est aussi à noter que Symfony s'appuie sur les entités pour créer et analyser les formulaires, qui seront étudiés plus tard.

Les entités **doivent** être hébergées dans le dossier `src/Entity`, là où Symfony s'attend à les trouver.

## Utilisation des entités

### Création des entités

Pour créer une entité, le moyen le plus simple est d'utiliser la commande du composant `Maker` :

```bash
symfony console make:entity
```

Cette commande engage un dialogue dans lequel on vous demandera :
1. Le nom de l'entité
2. Le type est les caractéristiques des propriétés de l'entité
3. Des options (en fonction du type) qui serviront principalement à la création du schéma relationnel.

Selon la logique les propriétés des entités peuvent représentées :
* des attributs, qui sont globalement des valeurs de type primitif ( `string`, `text`, `float`, `int`, `DateTime`...);
* des associations, qui définissent les relations entre entités, et qui peuvent être de plusieurs types (1-1 1-n/n-1 ou n-n)
* des généralisations, qui permettent d'organiser les entités en hiérarchies, de manière analogue au modèle de la programmation objet.

La commande `make:entity` peut-être appliquée plusieurs fois sur la même classe sans danger pour ajouter de nouvelles propriétés. En revanche, supprimer des propriétés (ou les modifier) doit être fait manuellement.

> **N.B.** Il existe une classe d'entité spéciale qui représente les utilisateurs de l'application, au sens de comptes qui permettent la connexion à l'application et la gestion des droits de ces utilisateurs. Abordée plus bas, cette entité doit être créée via la commande `make:user`.

#### Les attributs scalaires

Les attributs manipulés par Doctrine ne recouvrent pas exactement ceux de SQL. Par exemple, toutes les variantes des types d'entiers n'ont pas d'équivalents dans Doctrine.

On trouvera tous les types les plus simples, comme :
* `string`, `ascii_string` : équivalent au `VARCHAR` de SQL
* `text` : tous les types textes
* `integer`, `smallint`, `bigint` : tous les entiers
* `float` : tous les nombres à virgule flottante
* `decimal` : équivalent SQL
* `date`, `time`, `datetime` : équivalents à SQL
* `date_immutable`, `time_immutable`, `datetime_immutable` : variantes des précédents pour des valeurs immuables
* `datetimetz` : une date compte tenu du fuseau horaire
* `guid`: équivant à un `VARCHAR` de 32 caractères, généralement utilisé comme alternative aux clefs primaires auto-incrémentées (les GUID/UUID sont assurés d'être uniques)

Comme dans SQL, on peut définir des types binaires, qui sont toutefois très peu utilisés, voire considérés comme une mauvaise pratique :
* `blob`, `binary` : permettent de stocker dans la base de données des valeurs binaires arbitraires, comme des fichiers par exemple ;
* `object` : une propriété contenant un objet

De plus Doctrine reconnaît des données structurées, qui peuvent être stockées en tant aue telles dans la base de données :
* `array`, `simple_array`
* `json`, `json_array`

L'utilisation de champs JSON dans une base SQL doit toujours être décidée avec précaution. En effet, cela contrevient à la première forme normale (**1FN**) de SQL qui spécifie qu'une colonne doit toujours contenir des données _atomiques_.

Doctrine se charge naturellement de la correspondance entre les types définis dans les entités et ceux inscrits dans la base de données. Si l'on utilise le processus “automatisé”, Doctrine choisira pour les tables SQL des types qui semblent lui convenir mais, dans les limites de la compatibilité, il est tout à fait possible de modifier par la suite la structure de ces tables.

#### Les associations

Conformément au modèle Entité/Association, on peut définir trois types d'associations en fonction de leur cardinalité :
* `OneToOne` : spécifie une association dans laquelle chaque entité ne peut être liée qu'à une seule autre entité.
    * C'est une situation qui n'est pas très courante mais qui peut servir à definir des dépendances optionnelles (par exemple une Personne est liée à une entité Adresse mais celle-ci n'est pas obligatoire, on économise alors de l'espace disque) ou à des données temporaires (une Personne est liée à un Panier le temps de son achat en ligne — et le Panier n'appartient qu'à une seule Personne) ;
    > En SQL, on implémentera fréquemment cette association par le biais d'une clef étrangère entre les deux clefs primaires des deux tables liées, à moins de vouloir tracer un historique des associations.
* `OneToMany`, `ManyToOne` : caractérise une association asymétrique dans laquelle une des entités est liée à plusieurs autres entités.
    * Un exemple classique est le Disque qui peut contenir plusieurs Fichiers, mais un Fichier n'appartiendra qu'à un seul Disque.
    > En SQL, on ajouterait dans la table Enfant une colonne définissant une clef étrangère vers la table Mère.
* `ManyToMany` : indique une association dans laquelle les entités peuvent être liées à un nombre arbitraire d'autres entités.
    * Typiquement, les réseaux sociaux sont de cette nature : toute personne est liée à plusieurs autres personnes, qui elles-mêmes sont liées à plusieurs personnes, etc.
    > En SQL, on ne peut pas représenter directement ce type d'association ; on la décompose alors en deux associations 1-N tête-bêche au travers d'une table de jointure (ou de liaison)
  

Une notion importante à comprendre est que les associations de Doctrine **ne sont jamais strictement symétriques**. Pour des raisons internes, Doctrine détermine toujours un _sens directeur_ et un _sens inverse_. Cette distinction a des effets non négligeables lors du traitement des données par l'application (notamment au travers des formulaires) et nous en verrons des exemples par la suite. Il est important de comprendre qu'un choix **doit** être fait, lors de la conception de l'application, qu'il est relativement définitif, et qu'il peut rendre le code plus ou moins synthétique. En première approxiamtion, on dira que les deux entités dans une association n'ont pas le même _poids_ et que le sens directeur doit aller de l'entité la plus importante vers l'entité la plus annexe.
> Ainsi, à la création de la relation ManyToMany entre un `Post` et un `Tag` par exemple, il faudra se placer dans `Post` pour créer la relation si l'on veut pouvoir associer facilement ajouter des tags dans le formulaire de création d'un Post.
> **N.B.** Si vous n'êtes pas sûr du type d'association que vous voulez choisir, la commande `make:entity` vous propose le type `relation` qui vous guidera interactivement.

##### Comment identifier le sens directeur ?

Dans le code PHP de l'association (en admettant que l'on ait utilisé `make:entity`), on trouve un attribut tel que :
```php
/**
 * Identifiant du disque auquel appartient cette plage
 */
 #[ORM\ManyToOne(target="CD", inversedBy="tracks")]
 #[ORM\Column(type="integer")]
private $disqueID;
```
Cette annotation nous indique que la propriété `disqueID` est la référence d'une association de type `ManyToOne` vers l'entité `CD`. L'attribut `inversedBy` indique que `disqueID` est le point de départ du sens directeur de l'association. De l'autre côté, nous trouvons :
```php
/**
 * Liste des plages contenues dans ce CD
 */
 #[ORM\OneToMany(target="Track", mappedBy="disqueID")]
private $tracks;
```
Le sens inverse de l'association est marqué par l'attribut `mappedBy`.

Nous voyons ici que les associations sont purement **déclaratives**. Il est tout à fait possible de revenir manuellement sur la structure de ces associations... à condition d'être précis dans les modifications !

Le mécanisme est identique pour les autres formes d'associations. Néanmoins, dans le cas du couple `ManyToOne/OneToMany`, le sens directeur est _toujours_, obligatoirement, le sens `ManyToOne`.

#### Les généralisations

Il est tout à fait possible de répliquer avec Doctrine des hiérarchies d'entités comme on le fait dans la programmation objet.

Cet aspect des entités ne sera pas abordée dans cette partie, mais sera vue dans un module ultérieur.

#### Remarques finales

L'avantage de la commande `make:entity` est qu'elle peut être exécutée autant de fois qu'il est nécessaire sur la même entité, pour ajouter de nouvelles propriétés. Cela sera souvent très utile, évidemment, lorsque l'on souhaitera créer des associations... les entités liées devant déjà être connues au préalable.

En revanche, les cas de suppression de propriétés ne peuvent être réalisés que manuellement.

On remarquera que les propriétés ne sont pas toujours correctement réordonnées après modification de l'entité ; on sera quelquefois amenés à le faire manuellement.

Enfin, l'amoncellement de méthodes `set...` et `get...` pourrait être remplacé par les méthodes magiques PHP `__set` et `__get`. Nous voyons à ce propos qu'une des limites de Doctrine est de nécessiter des accesseurs et de mutateurs pour toutes les propriétés, ce qui n'est pas vraiment dans l'esprit de SOLID et réintroduit le problème des variables globales que la programmation objet cherchait à éliminer.

Une bonne partique de la POO tendrait à séparer la représentation interne des objets (les propriétés) de leur interface (les méthodes).

Lorsque Symfony crée l'entité, il est indiqué que _deux_ classes sont engendrées :
* L'entité proprement dite (dans le dossier `src/Entity`)
* Une classe dite `Repository` (dans le dossier `src/Repository`)

Nous retrouvons celle-ci dans une annotation en entête de la classe d'entité :
```php
/**
 #[ORM\Entity(repositoryClass=NewsletterRepository::class)]
 */
class Newsletter {
  /* ... */
}
```

Cette classe contiendra par la suite toutes les méthodes représentant des requêtes sur la base de données.
Les classes de requêtes `Repository` constitue le second volet de la gestion du modèle par l'ORM. 

### Le cas des utilisateurs

Il existe une (ou plusieurs) entité(s) spéciale(s) dans l'application, qui concerne la gestion des utllisateurs, ce qui est liée aux questions de sécurité de l'application.

Cette question de la sécurité est abordée plus loin, mais on peut dire qu'un utilisateur d'une application Symfony est représenté par trois caractéristiques :
- un identifiant
- un mot de passe
- des « rôles » qui permettront de savoir ce qu'il est autorisé à faire.

Symfony a une commande sépcifique pour créer les classes d'utilisateurs : `make:user`. Elle est très simple à utiliser et crée la structure minimale de la classe.

Une fois cette commande exécutée, ajouter des propriétés « générales » (e.g. nom, prénom, adresse, âge, etc.) se fait en ayant recours à `make:entity`.

## Modèle et base de données

### Création du schéma de la base de données

Une fois les entités créées, nous pouvons demander à Doctrine d'engendrer le schéma de la base de données.

(**N.B.** : en théorie, on peut aussi faire l'inverse, c'est-à-dire reconstituer les classes de l'application en fonction d'une base existante (retro-ingénierie) mais ses performances à reconstituer les classes reste aujourd'hui limitées)

1. En premier lieu, il faut définir l'adresse de la base de données. On la trouve dans le fichier de configuration `.env` qui est à la racine de l'installation
```bash
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```
Symfony vous propose plusieurs exemples de définition pour MySQL, PostgreSQL ou sqlite.
2. Ensuite vous devez créer la base de données. Pour cela il existe deux méthodes (au moins) :
* soit utiliser phpMyAdmin pour créer manuellement la base
* soit utiliser la commande `symfony console doctrine:database:create`, qui fait la même chose en se basant sur la déclaration du ficher `.env` (on peut ajouter l'option `--if-not-exists`)
  ```bash
  symfony console doctrine:database:create --if-not-exists
  ```


### Gestion du schéma de la base

Pour créer/modifier le schéma de la base de données, il existe deux méthodes.

#### Méthode historique (déconseillée)

Une fois la base créée, la première exécution est `doctrine:schema:create`. Cette commande, comme son nom l'indique, crée le schéma entier, sans tenir compte de ce qui existait déjà (potentiellement) dans la base de données. Ce commande peut être exécuté “à blanc” avec l'option `--dump-sql`.
```bash
symfony console doctrine:schema:create --dump-sql
```

Pour chaque modification des entités de l'application, vous pourrez maintenant exécuter la commande `doctrine:schema:update`. Celle-ci vous demandera _explicitement_ confirmation du mode d'exécution, soit “à blanc” (`--dump-sql`), soit effectivement (`--force`).
```bash
symfony console doctrine:schema:create --force
```

#### Méthode des migrations (conseillée)

Les versions récentes de Symfony intègrent un dispositif beaucoup plus perfectionné, les **migrations**.

La méthode historique a en effet une limite, c'est l'impossibilité de revenir en arrière. Toute modification du schéma est définitive. Les migrations résolvent ceci avec une gestion de versions qui permet de conserver la trace de toutes les modifications successives.

A chaque étape de création/modification des entités, il est possible d'associer une classe de migration, par la commande `make:migration`.
```bash
symfony console make:migration
```
Cette commande produit une nouvelle classe dans un dossier nommé `migrations`. Nous pouvons constater qu'une classe de migration contient principalement deux méthodes : `up` et `down`. Et chacune de ces méthodes contient une liste de commandes SQL de modification du schéma de la base.

Comme on peut s'y attendre `up` permet de passer à la version suivante et `down` de revenir à la version précedente.

Exécuter une migration — c'est-à-dire globalement mettre à jour le schéma de la base — se fait avec la commande `doctrine:migrations:migrate`.
```bash
symfony console doctrine:migrations:migrate
```
Par défaut, si vous modifiez la description de vos entités (avec `make:entity`, par exemple) et que vous créez une nouvelle classe de migration, `doctrine:migrations:migrate` devrait restaurer la correspondance entre les classes des entités et les tables de la base de données.

Les migrations peuvent être employées de manière plus avancée (cf. mémento ad hoc)
