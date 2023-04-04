# The HttpFoundation Component
Ressource :book:
https://symfony.com/doc/current/components/http_foundation.html

En PHP pur, vous utilisez les variables superglobales comme $_POST, $_GET, $_SESSIONS, exetera...

Le routeur de Symfony gère la centralisation de ces informations au sein d'une instance de la classe Request qui sera hydratée de toutes les superglobales.

Pour avoir accès à ses informations en bénéficiant des fonctions de Symfony, il vous faudra injecter la dépendance Request dans la méthode de votre contrôleur et utiliser les méthodes suivantes :

```php
public function new(Request $request): Response{
    // equivalent de $_GET["page"] http;//www.example.com/page=1
    $request->query->get("page");
    // equivalent de $_POST["page"]
    $request->request->get("page");
    // equivalent de $_COOKIE["user"]
    $request->cookies->get("user");

    //SESSIONS
    $session = $request->getSession();
    $session->get("cart");
    $session->set("cart","data");

    //Récuperer la page courante
    $referer = $request->headers->get('referer');

}
```
Les sessions peuvent aussi être récupérées directement grâce à l'injection de SessionInterface.
 ```php
public function new(SessionInterface $session): Response{
    $session->get("cart",[]);
    $session->set("cart","data");
}
```
        

## **Le petit plus** :thumbsup: 
Il existe une classe très utile utilisant les sessions, servant à afficher des messages à usage unique. Ce sont les "Flash Messages".
Ces variables dans les sessions ont la particularité de s'autodétruire à partir du moment où elles ont été lues.
Très utile pour notifier vos utilisateurs pour par exemple l'ajout d'un produit au panier, une inscription complète, ou n'importe quelle action qui vient de se passer.

### **Côté controller**
```php
$this->addFlash(
'type de messages',
'message'
);
```
**equivalent de**

```php
$session->getFlashBag()->add(
'types de messages',
'message'
);```
```

### **Côté Twig**
```twig
{% for message in app.flashes("type de message") %}
<div class="alert" role="alert">
    {{message}}
</div>
{% endfor %}```
```

**version like a boss**  
```twig
{% for label,messages in app.flashes %}
    {% for message in messages %}
        <div class="alert alert-{{label}}" role="alert">
            {{message}}
        </div>
    {% endfor %}
{% endfor %}
```
    



Pour les curieux :nerd_face:, allez voir /vendor/symfony/http-foundation/Session/Flash/FlashBag.php

Vous y trouverez le code source qui nous permet de comprendre comment fonctionne le système d'auto-destruction:
```php
 public function get(string $type, array $default = []): array
    {
        //ici on voit que la donnée est stockée dans une autre variable
        $return = $this->flashes[$type];
        //avant d'être détruire ici
        unset($this->flashes[$type]);
        //la valeur est retournée grâce à la variable tampon $return
        return $return;
    }
```