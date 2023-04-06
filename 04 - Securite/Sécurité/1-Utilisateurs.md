# Utilisateurs

## Création d'une entité pour les utilisateurs

Dans la grande majorité des cas d'application, un utilisateur fait partie du diagramme de classes. Dans ce cas, nous nous attendons à ce qu'il soit représenté par une entité.

Cette entité a une importance particulière, car elle est centrale dans la gestion des droits d'accès intégrée à Symfony. C'est pour cela qu'il existe une commande spécifique pour la création d'une l'entité des utilisateurs de l'application :
```bash
symfony console make:user
```
Cette commande a pour effet de créer une classe Entity qui contient les propriétés essentielles, à savoir :
* un identifiant (propriété à choisir)
* un mot de passe haché
* liste de rôles (pour la gestion ultérieure des droits).

De plus, en fonction des indications que nous aurons fournies à la console, Symfony modifie la configuration de la sécurité, dans le fichier `/config/packages/security.yaml`.

La classe engendrée, généralement nommée `User`, contient que les 3 propriétés vues au-dessus et est donc en général incomplète.
Nous serons donc amenés à exécuter la commande `make:entity` pour compléter la classe `User` avec nos propriétés supplémentaires.

> :warning: **Commande obsolète : ~~make:auth~~**
>
> Auparavant basé sur un `GuardAuthenticator`, le système d'authentification a depuis été grandement simplifié et amélioré à partir de la version 5 de Symfony.
> 
> `make:auth` est une commande anciennement très populaire qui n'est malheureusement pas à jour et qui génère des fichiers superflus et incompatibles avec la philosophie de Symfony 6. 

## La configuration dans le fichier `security.yaml`

### Fournisseurs (_providers_) d'entités

Les fournisseurs (_providers_) permettent à Symfony de savoir où trouver les données des utilisateurs, notamment lorsque ceux-ci cherchent à se connecter. Ils sont regroupés dans la section `providers` du fichier `security.yaml`.

```yaml
providers:
  # chaque fournisseur à une étiquette propre (arbitraire)
  standard_user:
    # Le mot-clef entity signale que les utilisateurs correspondent à une entité de l'application
    entity:
      # La classe qui supporte l'entité en question
      class: App\Entity\User
      # La propriété de la classe qui sert d'identifiant (important pour le processus de connexion)
      property: username
  # Il est possible d'avoir plusieurs sources
  test_user:
    # Ici des utilisateurs sont juste déclarés en mémoire, pour des besoins de tests par exemple
    memory:
      users:
          # Quelques utilisateurs
          # Naturellement, le mot de passe doit être compatible avec la politique de sécurité (e.g. hachage)
          john: { password: '...', roles: ['ROLE_ADMIN'] }
          jane: { password: '...', roles: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'] }
```
> [Documentation sur les providers](https://symfony.com/doc/current/security/user_provider.html#memory-user-provider)

### Les encodeurs

Naturellement, les mots de passe ne sont pas stockés “en clair” dans la base de données. Ils doivent être hachés. La section `encoders` décrit les différentes méthodes de hachage, relativement aux différents fournisseurs de profils.
```yaml
encoders:
  App\Entity\User:
    algorithm: auto
```
Il n'y a pas vraiment de raison de modifier la valeur d'`algorithm` configurée par défaut, car Symfony se chargera de sélectionner le meilleur algorithme disponible.


### La hiérarchie des rôles

Elément essentiel de la gestion des droits, les rôles doivent être déclarés, et ne le sont pas par défaut (via `make:user`). Ils sont regroupés de manière hiérarchique dans la section `role_hierarchy`. À droite des deux points, on indique tous les rôles dont les droits reviennent au rôle « de gauche ».
```yaml
role_hierarchy:
  ROLE_LIBRARIAN: ROLE_USER
  ROLE_SECRETARY: ROLE_USER
  ROLE_ADMIN: [ROLE_lIBRARIAN, ROLE_ACCOUNTANT]
  ROLE_SUPERADMIN: ROLE_ADMIN
```
`ROLE_USER` est le rôle par défaut des utilisateurs. Dans l'exemple, `ROLE_ADMIN` à tous les droits de `ROLE_LIBRARIAN` et de `ROLE_ACCOUNTANT` plus les siens propres.

Par ailleurs, il existe des rôles spécifiques pour préciser l'état d'identification des utilisateurs :
* ALLOWED_TO_SWITCH: Les utilisateurs autorisés à endosser l'identité d'autres personnes (généralement un super-administrateur) ;
* IS_AUTHENTICATED_REMEMBERED: Toutes les personnes connectées, en particulier celles qui ont activé l'option « Remember me » ;
* IS_AUTHENTICATED_FULLY: Les personnes qui se sont connectées via le formulaire de login ;
* IS_AUTHENTICATED_ANONYMOUSLY: Tous les utilisateurs ;
* IS_ANONYMOUS: Les seuls utilisateurs anonymes ;
* IS_REMEMBERED: Les seuls utilisateurs ayant choisi l'option « Remember me »
* IS_IMPERSONATOR: Les utilisateurs qui ont endossé l'identité d'un autre utilisateur

Ces différents rôles pourront bien sûr être testés par la suite, lors des autorisations d'accès à certaines ressources.