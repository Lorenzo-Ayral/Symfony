# Aide-mémoire sur la sécurité

## Utilisateurs

### Commandes

| Commande | Explication |
|----|-----|
| make:user | Crée  d'une classe d'utilisateur, spécifiquement dédiée aux questions de sécurité (authentification et droits d'accès) |
| make:registration-form | Crée toutes les ressources nécessaires au prcessus de création d'ub nouvel utilisateur |

### Fonctions

Fonctions utilisables directement dans les contrôleurs (héritées de `AbstractContoller`)

| Méthode | Arguments | Explication |
|-|-|-|
| isGranted() | string|array | Vérifie que l'utilisateur dispose des droits liés à un certain rôle |
| denyAccessUnlessGranted() | string|array | Refuse l'accès en cas d'insuffisance de droits eet lève une exception |
| getUser() | | Retourne l'entité utilisateur (si elle existe après qu'une personne se soit connectée) |

### Annotations

| Annotation | Arguments | Explication |
|-|-|-|
| @IsGranted | string|array | Identique à la fonction `isGranted()`, mais intervient plus tôt dans le cycle de la requête |
| @Security | string, entity | Plus flexible que `@IsGranted` car permet de passer comme argument une expression (cf. [ExpressionLanguage(https://symfony.com/doc/current/reference/constraints/Expression.html)]) et un objet comme cible ou contexte de l'autorisation |


### Extensions Twig

| Syntaxe | Arguments | Explication |
|-|-|-|
| app.user | | Accès à l'utilisateur (connecté) directement dans les gabarits Twig |
| is_granted | string|array | Vérification des droits d'accès directement dans les gabarits Twig |

### Rôles natifs

Les rôles ont tous le préfixe « `ROLE_` »

| Rôle | Explication |
|----|-----|
| ROLE_NO_ACCESS | Utilisateur sans droit d'accès (doit être géré par l'application)   |
| ROLE_USER | Rôle par défaut de tout utilisateur connecté |
| ROLE_ADMIN | Rôle généralement dédié aux administrateurs du site |
| ROLE_SUPER_ADMIN | Rôle généralement accordé au super-administrateur du site |
| ROLE_ALLOWED_TO_SWITCH | Utilisateur autorisé à prendre l'identité d'un autre utilisateur |

#### Détermination des degrés d'authentification

| Rôle | Explication |
|----|-----|
| PUBLIC_ACCESS | Accès non restreint |
| IS_AUTHENTICATED_REMEMBERED | Utilisateur connecté, dont la session a expiré, mais qui a activé l'option « Remember me » |
| IS_AUTHENTICATED_FULLY | Utilisateur dont l'authentification a été validée
| IS_IMPERSONATOR | Utilisateur ayant endossé l'indentité d'un autre utilisateur |

## Voteurs

## Résumé des syntaxes

| Méthode | Arguments | Explication |
|----|-----|
| supports | string, any | Est déclenché avant que ne soit déterminé quel contrôleur doit être appelé |
| voteOnAttribute | string, any, TokenInterface | Est déclenché une fois le contrôleur déterminé, mais avant son exécution |

### Tags

| Tag | Explication |
|----|-----|
| security.voter | Agrège un voteur au système de décision des droits d'accès  |

### Configuration

| Label | Propriété | Explication |
|----|-----|
| access_decision_manager | service | Définit une classe pour une gestion personnalisée des décisions d'accès  |

### Interface `Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface`

| Méthode | Arguments | Explication |
|----|-----|
| handle | Request, AccessDeniedException | Implémente la logique de prise en charge des erreurs |
