## Aide-mémoire

## Entités

### Commandes

| Commande                   | Explication                                                   |
|----------------------------|---------------------------------------------------------------|
| make:entity                | Création d'un entité                                          |
| doctrine:create:database   | Création d‘une base de données                                |
| doctrine:schema:create     | Création du schéma d'une base de données                      |
| doctrine:schema:update     | Mise à jour du schéma d'une base de données                   |
| make:migration             | Création d‘une migration                                      |
| doctrine:migration:migrate | Mise à jour du schéma d'une base de données via une migration |

### Attributs

| Attribut               | Explication                                                  |
|--------------------------|--------------------------------------------------------------|
| @ORM\Entity              | Attributs de la classe d'entité                              |
| @ORM\OneToOne            | Association 1-1                                              |
| @ORM\ManyToOne           | Association N-1                                              |
| @ORM\OneToMany           | Association 1-N (réciproque de la précédente)                |
| @ORM\ManyToMany          | Association N-N                                              |
| @ORM\Column              | Correspondance de la propriété dans la base de données       |

## Données factices

### Paquetages

| Package               | Recipe | Explication                                                 |
|-----------------------|--------|-------------------------------------------------------------|
| **orm-fixtures**      | *      | Le bundle DataFixtures de Doctrine                          |
| **fakerphp/faker**    |        | FakerPHP, outil pour engendrer des données “plausibles”     |
| **zenstruck/foundry** |        | Bibliothèque moderne pour les fixtures, sur-couche de Faker |

### Commandes

| Commande                   | Explication                                                               |
|----------------------------|---------------------------------------------------------------------------|
| **make:fixtures**          | Création d'une classe de Fixture                                          |
| **make:factory**           | Création d'une classe de Fixture pour Zenstruck Foundry                   |
| **doctrine:fixtures:load** | Engendrement des données factices avec le bundle DataFixtures de Doctrine |

### Fonctions

#### DataFixtures

| Fonction            | Signature       | Explication                                                                            |
|---------------------|-----------------|----------------------------------------------------------------------------------------|
| **load**            | (ObjectManager) | Fonction de la classe de fixtures qui définit les données à créer                      |
| **getDependencies** | ()              | Etablit les dépendances entre entités pour l'ordonnancement de la création de fixtures |
| **getGroups**       | ()              | Définit de groupes pour segmenter la création de fixtures                              |


## Classes héritant de `ServiceEntityRepository`

### Méthodes de recherche par défaut

| Méthode de Repository | Signature       | Explication                                                            |
|-----------------------|-----------------|------------------------------------------------------------------------|
| find                  | (int)           | Chercher une entité d'après son id                                     |
| findAll               | ()              | Chercher toutes les entités d'un certaine classe (déconseillé)         |
| findOneBy             | (string, mixed) | Trouver une entité selon la valeur d'une propriété                     |
| findBy                | (string, mixed) | Trouver toutes les entités en fonction de la la valeur d'une propriété |

### Utilitaires des classes de requêtes

| Méthode de Repository | Signature | Explication                                        |
|-----------------------|-----------|----------------------------------------------------|
| getEntityManager      | ()        | Accéder à au gestionnaire d'entités de de Doctrine |

### Mode PDO du gestionnaire d'entités

| Méthode de EntityManager | Signature | Explication                                                                                                |
|--------------------------|-----------|------------------------------------------------------------------------------------------------------------|
| getConnection            | ()        | Connexion à la base de données via la déclaration du fichier .env (équivaut à `new PDO(...)` en PHP natif) |

| Méthode de Doctrine\DBAL\Connection | Signature | Explication                                                              |
|-------------------------------------|-----------|--------------------------------------------------------------------------|
| fetchAll                            | ()        | Méthode englobant toutes les étapes : préparation, exécution, expédition |
