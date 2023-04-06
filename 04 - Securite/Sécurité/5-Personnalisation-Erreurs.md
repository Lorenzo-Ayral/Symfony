

#### Personnalisation des erreurs

Si le droit d'accès est refusé, Symfony déclenche une réponse HTTP avec le code d'erreur 403 "Forbidden Access". Si nous voulons personnaliser ces erreurs, nous avons plusieurs outils à notre disposition.

##### Personnalisation du message d'erreur

Dans un premier temps, nous pouvons ajouter un message d'erreur dans l'appel de la méthode `denyAccessUnlessGranted` :
```php
$this->denyAccessUnlessGranted('edition', $comment, "Seules les personnes qui ont emprunté un document peuvent le commenter");
```

Notons qu'en cas de refus d'accès, l'exception déclenchée est de la classe `Symfony\Component\Security\Core\Exception\AccessDeniedException` qui hérite de la classe PHP `\RuntimeException`.

##### Personnalisation de la page d'erreur

Par défaut, Symfony renvoie une page standard, qui diffère néanmoins selon si l'on est en environnement `dev` ou `prod`. En production, on n'affichera évidemment pas les informations de déboguage, en particulier la trace.

Pour créer ses propres page d'erreur, il suffit d'implémenter un gabarit dans le dossier : `templates/bundles/TwigBundle/Exception`. Pour les erreurs « génériques » (500, etc.), le fichier s'appelle tout simplement `error.html.twig`. Les erreurs 403 et 404 peuvent être affichées dans des pages spécifiques nommées `error403.html.twig` et `error404.html.twig`.

Symfony passe trois variables, `status_code` et `status_text` (texte) ainsi que `exception` (objet). Cette dernière variable permet d'avoir accès à davantage d'information, notamment `subject` qui contient l'objet sur lequel la décision porte ou `traceAsString`, la trace de l'erreur sous forme de chaîne de caractères (héritée de PHP).

##### Personnalisation de la gestion d'erreur

Pour aller plus loin, il est possible d'implémenter sa propre classe de prise en charge des erreurs.

Dan ce cas, nous avons recours à l'interface `Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface`. Elle nous oblige à implémenter une méthode `handle` :
```php
public function handle(Request $request, AccessDeniedException $accessDeniedException);
```
* `$request` est la requête HTTP soumise par le navigateur, comme dans les contrôleurs,
* `$accessDeniedException` est l'exception déclencée par le refus d'accès.

Cette méthode retourne un objet `Response` arbitraire.

Exemple :
```php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
// Import de Twig comme service pour faire le rendud'une page, par exemple

use Twig\Environment;
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $twig;

    // Injection de la dépendance
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        // Provoque le rendu d'une page selon un gabarit du dossier /templates/error/ (par exemple)
        $html = $this->twig->render('error/example.html.twig', [
            'someVariable' => 123
        ]);
        //on pourrait par exemple envoyer un mail à l'administrateur en cas d'erreur anormale

        return new Response($html, 403);
    }
}
```

## Ressources

* [Personnalisation des pages d'erreur](https://symfony.com/doc/current/controller/error_pages.html)
* [Personnalisation du gestionnaire d'erreurs](https://symfony.com/doc/5.0/security/access_denied_handler.html)
* [La classe RuntimeException de PHP](https://www.php.net/manual/en/class.runtimeexception.php)
