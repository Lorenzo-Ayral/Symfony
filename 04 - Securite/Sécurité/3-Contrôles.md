# Le contrôle d'accès

Il existe dans Symfony quatre méthodes pour filtrer les accès aux resources :
1. par l'`acces_control` du fichier `security.yaml`
2. par l'ajout d'attributs sur nos routes
3. par l'insertion de code spécifique dans les méthodes de l'application ou les gabarits Twig
4. par la définition de règles algorithmiques avec les classes de “voteurs”


## access_control

La méthode par l'`access_control` offre le moyen le plus simple de définir des filtres d'accès. Néanmoins, elle peut s'avérer sous-optimale, car de granularité assez grossière. Il se peut aussi qu'elle ne soit pas réellement applicable en fonction de la politique d'URL qui a été adoptée pour l'application. Néanmoins, elle peut servir à résoudre les cas les plus simples.

Ce type de contrôle se définit dans le fichier `security.yaml`, sous l'étiquette racine `security` :
```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: /comments, roles: [ROLE_COMMUNITY, ROLE_LIBRARIAN] }
    - { path: ^/profile, roles: ROLE_USER }
```
Dans de mode d'autorisation, un schéma d'URL (`path`), défini par une expression régulière, n'est accessible qu'aux personnes ayant le ou les rôles listés dans `roles`.

Les points d'accès ne sont pas uniquement définis par des chemins. On peut également filtrer :
* par adresse IP : `ip`
* par nom d'hôte: `host`
* par port : `port`
* par méthode : `methods`


## Les méthodes de filtrage

Le composant `Security` fournit des méthodes qui sont, comme d'habitude, directement accessibles dans les contrôleurs (le filtrage des accès n'ayant pas grand sens dans d'autres contextes).

Il existe plusieurs variantes de l'application de filtres. D'une manière générale, l'emploi de ces méthodes à l'avantage d'une granularité fine puisque le contrôle est effectué au niveau de chaque méthode. La contrepartie est un coût qui peut être élevé (en termes d'écriture) puisqu'il faudra répéter cela sur toutes les actions des contrôleurs.

### Par attribut

### @IsGranted

```php
/**
 * Affiche la liste des auteurs
 */
 #[IsGranted("ROLE_ADMIN")]
 #[Route("/authors", name="authors_list")]
public function index(): Response
{
    return $this->render("default/index.html.twig", [
        'authors' => self::AUTHORS
        ];
    ) 
}
```
Ici, l'attribut `@IsGranted` applique le filtrage sur une route spécifique et empêche la méthode de s'exécuter (donc gain de temps dans le cycle de la requête). La limite de l'attribut est qu'elle ne reconnaît que des rôles.

### @Security
Il existe une alternative utilisant un composant de Symfony nommé `ExpressionLanguage` qui permet de faire des choses un peu plus sophistiquées par le biais d'expressions logiques complexes. Exemple :
```php
#[Security("is_granted('ROLE_ADMIN') or user==post.getUser()")]
public function index(): Response
{ /* ...*/ }
```
Cet exemple permet de dire "Soit l'utilisateur possède le rôle ADMIN, soit il est l'auteur de l'article".
<br>
Ressource :book: L'attribut Security https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#security



### avec `denyAccessUnlessGranted`

```php
/**
 * Affiche la liste des auteurs
 */
 #[Route("/authors", name="authors_list")]
public function index(): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN')

    return $this->render("default/index.html.twig", [
        'authors' => self::AUTHORS
        ];
    ) 
}
```
Alternativement, on peut utiliser la méthode `denyAccessUnlessGranted`. Cette version n'est pas très différente, mais elle intervient _après_ le lancement de l'exécution du contrôleur. Ceci devrait être logiquement moins efficace. Mais nous verrons que son spectre d'emploi est plus large.

### avec `isGranted`

```php
/**
 * Affiche la liste des auteurs
 */
 #[Route("/authors", name="authors_list")]
public function index(): Response
{
    if ($this->isGranted((ROLE_ADMIN))) {
        return $this->render("default/index.html.twig", [
            'authors' => self::AUTHORS
            ];
        ) 
    } else {
        $this->redirectToRoute(/* ... */)
    }
}
```
La méthode `isGranted` est moins violente que la précédente, car elle fait que poser le diagnostic. Au développeur de choisir ensuite quelle conséquence tirer de la réponse.

### Filter dans les gabarits Twig

Il est possible de filtrer les accès dans Twig. Il existe une extension avec la fonction `is_granted`, qui joue exactement le même rôle que la fonction précédente.
```twig
{% block article %}
    <h1>{{ titre }}</h1>
    {% if is_granted('ROLE_ADMIN') %}
        <div class="edit-option">
            <button><a href="{{ path("content_edit", {id:5}) }}">Modifier</a></button>
        </div>
    {% endif %}
    <p>...</p>
{% endblock %}
```
Dans cet exemple, un bouton pour modifier un contenu n'est affiché que si l'utilisateur est un administrateur.

### Récupérer l'objet “Utilisateur”

Le composant `Security` fournit aussi une méthode permettant d'accéder à l'objet représentant l'utilisateur connecté (si c'est le cas).

#### Dans un contrôleur

Avec la fonction `getUser()` :
```php
/**
 * Affiche la liste des auteurs
 */
 #[Route("/authors", name="authors_list")]
public function index(): Response
{
    $user = $this->getUser();

    return $this->render("default/index.html.twig", [
        'authors' => self::AUTHORS, 
        'firstName' => $user->getFirstName()
        ];
    ) 
}
```

#### Dans un gabarit Twig

Avec la propriété `user` de la variable globale `app` :
```twig
{% block header %}
    <h1>{{ titre }}</h1>
    <div>
        <nav>
        /* ... */
        </nav>
        <div>{{ app.user.firstName }}</div>
    </div>
{% endblock %}
```

## Les voteurs

La méthode la plus puissante pour filtrer les accès consiste à utiliser des classes de voteurs, qui sont une extension de la notion d'ACL (Access Control List). Les voteurs offrent une variété infinie de règles, d'une complexité arbitraire.

(cf. [Notes de cors sur les voteurs](4-Voteurs))

