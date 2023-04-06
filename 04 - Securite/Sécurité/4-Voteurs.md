# Voteurs

## Introduction

Les **voteurs** sont une fonctionnalité avancée de Symfony pour décider du droit d'un utilisateur à accéder à une certaine ressource. Dans de nombreux cas, en effet, les solutions précédentes ne peuvent pas fonctionner car elles sont principalement centrées sur les **rôles**, que ce soit `access-control` ou les annotations `isGranted`.

Dans de nombreux systèmes (notamment les systèmes d'exploitation), il existe des **ACL** (ou Access Control List) qui permettent de définir la notion de “propriétaire” (ou créateur) et de “proximité” (appartenance à un même groupe que le créateur) et cette technique a été historiquement employée par Symfony, avant d'être abandonnée au profit des voteurs.

Ces derniers étendent en effet les ACL à des procédures de décision arbitrairement complexes. Nous allons pouvoir être capables de calculer, sur la base de critères qui nous sont propres, une valeur d'acquiescement ou de refus. Qui plus est, nous allons pouvoir demander plusieurs avis et procéder à un vote sur des bases que nous choisirons, comme par exemple l'unanimité de l'acquiescement.

Les voteurs représentent donc un mode très sophistiqué de jugement, qui peut simuler les méthodes de prise de décision dans les organisations.

En réalité, même lorsque nous utilisons les contrôles d'accès basés sur les rôles, Symfony fait appel (silencieusement) aux voteurs, ce qui en fait l'infrastructure centrale de toute la gestion de la sécurité de la plate-forme.

Les voteurs lient entre eux trois objets :
- un utilisateur (au sens de Symfony),
- une action exercée par l'utilisateur,
- un objet sur lequel s'exerce l'action.

Globalement, on peut dire qu'un voteur décide si quelqu'un peut exercer une action sur un objet. Par exemple : 

`````« Un visiteur anonyme peut-il lire (_action_) la fiche d'un livre (_objet_) ? »`````

Du point de vue du code, les voteurs s'utilisent comme les autres contrôles d'accès, avec `isGranted` ou `denyAccessUnlessGranted`.
```php
// Appel dans un contrôleur de l'exemple précédent
$book = $repository->find(214);
$this->denyAccessUnlessGranted('READ', $book);
```
On remarque que l'action `READ` prend ici la place du rôle dans l'utilisation précédente du contrôle d'accès fondé sur les rôles et que l'on ajoute l'objet (qui reste optionnel, on n'en a pas toujours besoin).

### Implémentation

De manière très simple, un “voteur” est une sous-classe de `Symfony\Component\Security\Core\Authorization\Voter\Voter`.

Le point fondamental des voteurs est qu'ils interviennent dans le contexte de certaines actions. Donc tous les voteurs ne participent pas à toutes les prises de décision. A chaque fois qu'un utilisateur demande à accéder à une ressource, Symfony convoque les voteurs qui sont signalés comme “concernés” par cette demande et sollicite leur avis.

La première chose à définir est donc la liste des actions sur lesquelles le voteur devra se prononcer. En général, l'avis concernera aussi une (ou plusieurs) entités de l'application (un commentaire, un profil utilisateur, etc.).

Le squelette minimal d'une classe de voteurs s'écrit ainsi :
```php
namespace App\Security;

// Dans cete exemple, le voteur aura à se prononcer sur l'accès aux commentaires
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

// Les noms des classes de voteurs portent le suffixe `Voter` (ici `PostVoter`)
class PostVoter extends Voter
{
  // Chaque constante représente une action, à laquelle est attribué une étiquette tout à fait arbitraire (ici 'edition')
  const EDIT = "edition";
  /* ... */
}
```

Pour créer une classe de voteurs, il existe une commande __ad hoc__ :
```bash
symfony console make:voter <NomDeClasse>
```

#### Interface

En tant que sous-classe de `Voter`,  nos classes de décision implémentent l'interface `Symfony\Component\Security\Core\Authorization\Voter\VoterInterface`.

Au passage :
1. Notre classe est bien entendu considérée comme un service (en dehors de toute indication explicitement contraire) ;
2. Comme elle implémente `VoterInterface`, elle est directement agrégée par l'auto-configuration aux classes étiquetées ("taguées") `security.voter`.

L'interface `VoterInterface` oblige à implémenter deux méthodes : `supports` et `voteOnAttribute`.

##### La méthode `supports`

La méthode `supports` nous permet d'indiquer à Symfony qu'un voteur est concerné par une prise de décision. Pour cela, nous avons besoin de deux paramètres :
```php
abstract protected function supports(string $attribute, $subject);
```
* `$attribute`, le nom d'une certaine action sous forme de chaîne de caractère ;
* `$subject`, dont le type n'est pas précisé, sera en général un objet (une entité), mais il reste optionnel, c'est l'objet dont nous parlions plus haut sur lequel vas s'exercer l'action.

Bien que cela ne soit pas précisé, Symfony attend que `supports` retourne une valeur booléenne, indiquant que le voteur participe ou non à la décision. Le corps de la fonction va consister à calculer cette valeur booléenne.

Exemple :
```php
protected function supports(string $attribute, $subject): bool
{
    /*
     * Le voteur ne se prononce que si l'action est 'comment' et le sujet un Document 
     */
    if ($attribute !== 'comment' || !($subject instanceof Document)) {
        return false;
    }
    
    return true;
}
```

##### La méthode `voteOnAttribute`

Cette seconde méthode est appelée dans le cas où `supports` retourne `true`. `voteOnAttribute` est alors chargée de rendre un avis, favorable ou non, à la demande d'accès à la ressource. L'algorithme, ici encore, est laissé à l'entière discrétion des développeurs de l'application.
```php
abstract protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token);
```
Comme nous le voyons, `voteOnAttribute` reçoit les mêmes valeurs que `supports` (cette méthode n'est jamais appelée directement), auxquelles s'ajoute un jeton :
* `$token`, jeton qui permet d'avoir accès aux données de l'utilisateur connecté (si c'est le cas)

A l'instar de `supports`, `voteOnAttribute` retourne une valeur booléenne indiquant si l'utilisateur est autorisé à accéder à la ressource, ou non.

Naturellement, la méthode `voteOnAttribute` peut déléguer à des méthodes spécialisées (qui seront généralement privées) la tâche de réaliser certaines parties du calcul.

#### Multiplicité des voteurs

Le gros intérêt des voteurs réside dans le fait qu'un nombre indéterminé de voteurs peut être améné à participer à une décision. Il suffit pour cela qu'ils déclarent la même action sur le même type d'entité. Ceci implique que les avis peuvent être divergents. Comment décider dans ce cas ? Nous allons alors devoir décider d'une stratégie de décision.

Nativement, Symfony connaît quatre stratégies :
* `affirmative` : valide dès qu'un seul voteur a rendu un avis positif (c'est la stratégie par défaut) ;
* `consensus` : adopte une stratégie “majoritaire” (50% + 1 voix) ;
* `unanimous` : requiert l'unanimité des voteurs pour valider l'autorisation ;
* `priority` : rend un avis conforme au premier voteur qui a parlé.

Toutes les composantes de la stratégie sont déclarées dans le fichier `security.yaml` :
```yaml
security:
    access_decision_manager:
        # stratégie adoptée
        strategy: unanimous
        # que faire si aucun voteur ne rend un avis
        allow_if_all_abstain: false
        # que faire si la stratégie est majoritaire et le nombre de voix égal pour l'autorisation et le refus
        allow_if_equal_granted_denied: false
```

Pour pouvoir utiliser les priorités (dans la stratégie `priority`), nous devrons déclarer explicitement notre classe de voteurs comme service.
> **N.B.** Tous les services, depuis Symfony 4.4, peuvent se voir allouer une priorité.

```yaml
services:
    PostVoter:
        tags:
            - { name: 'security.voter', priority: 10 }
    Post2Voter:
        tags:
            - { name: 'security.voter', priority: -5 }

```

##### Définir sa propre stratégie de décision

Dans le cas où les stratégies natives de Symfony ne vous conviendraient pas, il reste possible d'écrire sa propre classe de prise de décision. Pour cela, la classe en question devra implémenter l'interface `Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface` et être déclarée dans le fichier de configuration.
```yaml
security:
    access_decision_manager:
        # FQCN de la classe à utiliser
        service: App\Security\CustomDecisionManager
```
L'interface requiert l'implémentation de la méthode `decide`.
```php
public function decide(TokenInterface $token, array $attributes, $object = null);
```
Les paramètres sont en phase avec ceux des classes de voteurs
* `$token`, un jeton  permettant d'identifier l'utilisateur connecté ;
* `attributes`, un tableau d'attributs représentant des actions ;
* `$object`, un objet (optionnel) sur lequel doit s'exercer l'action.

Le rôle de la méthode `decide` est d'implémenter le processus de décision, qui va donc convoquer les voteurs et agréger les décisions individuelles. Voici par exemple le code du processus de décision de type `affirmative` :
```php
private function decideAffirmative(TokenInterface $token, array $attributes, $object = null): bool
{
    $deny = 0;

    // Les voteurs sont recueillis par le constructeur
    foreach ($this->voters as $voter) {
        // Chaque voteur est convoqué
        // La méthode `vote` est implémentée dans la classe abstraite ; il n'est pas nécessaire de la réécrire
        // Cette méthode exécute en cascade `supports` et `voteOnAttribute`
        $result = $voter->vote($token, $object, $attributes);

        // Dès qu'un voteur a rendu un avis positif, l'autorisation est donnée
        if (VoterInterface::ACCESS_GRANTED === $result) {
            return true;
        }

        if (VoterInterface::ACCESS_DENIED === $result) {
            ++$deny;
        }
    }

    if ($deny > 0) {
        return false;
    }

    // Au cas où aucun voteur n'a rendu d'avis, la décision est conforme à la configuration (et par défaut `true`)
    return $this->allowIfAllAbstainDecisions;
}
```
On note que :
* l'ordre des paramètres n'est pas identique à celui utilisé dans les classes de voteurs (!).
* les attributs (actions) peuvent être un tableau (c'est-à-dire, on peut voter pour plusieurs actions simultanément)

### Usage

#### Vérification des droits d'accès

Une fois toutes les classes de voteurs et la configuration mises en place, l'utilisation n'est pas différente des modes déjà vus. Il suffit de remplacer les rôles par des étiquettes d'action et éventuellement un objet. Par exemple dans un contrôleur, l'accès se fait directement comme ceci :
```php
/*
 * En reprenant l'exemple du début de la page
 */
$comment = $commentRepository->find(54);
// L'utilisateur n'a peut-être pas le droit de modifier un commentaire s'il n'en est pas l'auteur
$this->denyAccessUnlessGranted('edition', $comment);
```



## Ressources

* [Les classes de voteurs](https://symfony.com/doc/current/security/voters.html#declaring-the-voter-as-a-service)
* [Les modes d'autorisation d'accès](https://symfony.com/doc/current/components/security/authorization.html)
* [Affecter des priorités aux services étiquetés](https://symfony.com/blog/new-in-symfony-4-4-dependency-injection-improvements-part-2)
