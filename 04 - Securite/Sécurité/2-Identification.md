# Profil et identification

## Introduction

La gestion des profils utilisateurs comprend deux volets distincts :
- la création de nouveaux profils via un formulaire d'enregistrment ;
- le processus d'identification et d'authentification lors de l'accès à l'application.

## Enregistrement

Pour permettre la création d'un nouveau profil utilisateur, il existe évidemment de nombreuses méthodes, mais la plus commune est sans doute la mise à disposition d'un formulaire d'enregistrement.

Dans ce cas, il existe une commande qui fera l'essentiel du travail :
```bash
symfony console make:registration-form
```

Cette commande crée toute la chaîne nécessaire au processus : une classe de contrôleurs, un formulaire, un gabarit Twig.

> **N.B.** Par défaut, la commande vous propose d'intégrer la validation de création du profil par mail, ce qui nécessite l'installation de bibliothèques supplémentaires. Nous verrons cette option dans le prochain module sécurité avancé.

Si vous voulez renforcer la sécurité, vous pouvez modifier le formulaire de manière à intégrer des champs de type `RepeatedType`. Ceci est très utile car Symfony affichera dans la page **deux** champs pour la même propriété, permettant ainsi à l'utilisateur de répéter la saisie d'une information critique, comme le mot de passe, afin que le risque d'erreur soit très limité.

## Connexion/Déconnexion

La question de savoir comment les utilisateurs accèdent à l'application comporte aussi deux aspects :
- le découpage de l'application en différentes parties accessibles selon des modes définis
- la mise en œuvre de l'identification et de l'authentification des utilisateurs.

### Différents modes d'accès ou pare-feux (_firewalls_)

Les pare-feux (_firewalls_) sont une technique pour séparer les applications en “zones” étanches les unes vis-à-vis des autres et donc de ne permettre que des accès limités à certaines parties. C'est la fonctionnalité la plus sophistiquée de la sécurité de Symfony, mais elle est en fait peu utilisée. Dans la majorité des cas, on se contentera des deux pare-feux par défaut. Le premier `dev` isole les ressources sans contrôle d'accès, c'est-à-dire les ressources publiques. Le second, appelé `main`, gère l'accès à toutes les autres ressources (dont les « pages » de l'application).

```yaml
firewalls:
  # La zone 'dev' regroupe les ressources qui ne sont pas soumises à la gestion de la sécurité
  # On y trouve les ressources web publiques et le profileur
  dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
  # La zone main regrooupe les ressources qui peuvent être soumises à un droit d'accès
  main:
      #Le schéma des routes isolées par le pare-feu (la “zone”)
      pattern: ^/
      # Les utilisateurs anonymes sont autorisés... dans certaines limites
      anonymous: true
      # 'lazy` permet de ne charger la session que si un contrôle d'accès 
      # est véritablement effectué lors de la requête
      lazy: true
  ```

Chaque zone associée à un pare-feu définit un schéma (ou « _pattern_ ») qui rassemble les URL incluses dans cette zone. Quand il n'y a pas d'ambiguïté, ce schéma peut être laissé implicite.

Chaque zone définit également son propre mode de connexion, c'est ce qui fait l'intérêt du mécanisme. On peut tout a fait imaginer un accès web par formulaire, paralèlement à un accès avec un jeton d'API.

Nous nous limitons ici à la connxexion la plus simple, par formulaire.

### Connexion (login)

Dans le cas, courant, de connexion par formulaire, nous devons spécifier dans le pare-feu `main` quels sont les paramètres de la connexion, ce qui consiste principalement à préciser la route qui affiche le formulaire :

```yaml
form_login:
    # contrôleur permettant d'afficher le formulaire
    # login est le nom (arbitraire) de la route 
    login_path: login
    # contrôleur de traitement du formulaire de connexion
    # on remarque que la route est identique.
    check_path: login
    # redirection par défaut après la réussite de la connexion
    default_target_path: home
                
```
Compte tenu de la configuration ci-dessus, voici à quoi ressemblerait le contrôleur associé :
```php
namespace App/Controller;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupèreles erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        // Affiche l'identifiant du dernier utilisateur connecté
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
```

On remarque que le code ne prend en charge que la gestion des erreurs s'il y'en a et l'affichage du formulaire. Le processus d'authentification reste occulté dans sa version par défaut. Ceci rend la gestion de l'authentification extrêmement simple à mettre en œuvre. Il existe des moyens pour reprendre la main sur ce processus, en créant des **classes d'authentification**. Mais celles-ci, hors besoin spécifique, ne sont pas recommandées par les dernières versions de Symfony.

En environnement de développement, vous pourrez vérifier dans le profileur que la connexion a réussi en inspectant l'identifiant et les paramères de l'utilisateur connecté.

#### Le formulaire de connexion

Dans cette optique, il faut naturellement un formulaire à disposition des utilisateurs. Une fois n'est pas coutume dans Symfony, le formulaire de connexion est un vrai formulaire HTML :

```html
    <form action="{{ path('ogin') }}" method="post">
        <label for="username">Email:</label>
        <input type="text" id="username" name="_username" value="{{ last_username }}"/>

        <label for="password">Password:</label>
        <input type="password" id="password" name="_password"/>

        {# Contrôle de la redirection (facultatif si défini dans security.yaml ) #}
        <input type="hidden" name="_target_path" value="/account"/> #}
        
        {# Jeton de sécurité CSRF #}
        <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">

        <button type="submit">login</button>
    </form>
```

Plusieurs choses sont à remarquer dans ce formulaire :
1. Le premier `input` a pour attribut `name`: `_username` ; c'est un nom générique qui sera associé par Symfony à la propriété de l'entité que vous avez choisie comme identifiant (cf. `providers`du fichier `security.yaml`).
2. De même, le second `input` a pour nom `_password`, un mot-clef spécial
3. On note la présence d'un champ caché `_csrf_token` qui est un jeton de sécurité généré par Symfony pour empêcher les attaques CSRF (tout formulaire devrait en inclure un).

Pour s'assurer que la protection est activée, vérifiez qu'elle est présente dans la section `form_login` du pare-feu :
```yaml
firewalls:
# ...
  main:
  # ...
    form_login:
    # ...
      enable_csrf: true
```

### Déconnexion (logout)

Pour la déconnexion, la logique est la même. On ajoute dans le pare-feu `main` la section suivante :

```yaml
logout:
    path: logout
    # Route de redirection après la déconnexion
    target: home
```

Le couple route/contrôleur `logout` doit juste exister dans une classe. Le corps de la méthode est généralement vide, le processus étant pris en charge par Symfony automatiquement.

[Documentation officielle](https://symfony.com/doc/current/security.html#logging-out
)

Vous devrez naturellement ajouter un bouton déconnexion dans votre template de base.


## Ressources

- [Documentation officielle sur la sécurité](https://symfony.com/doc/current/security.html)
- [Documentation sur les providers](https://symfony.com/doc/current/security/user_provider.html#memory-user-provider)