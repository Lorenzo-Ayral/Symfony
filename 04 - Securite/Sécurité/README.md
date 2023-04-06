# Utilisateurs, sécurité et droits d'accès

## Présentation

Ce module a trait à la sécurisation des applications. Le point central de la sécurité dans Symfony est la notion d'**utilisateur**. Nous avons vu (cf. Entités) que les utilisateurs faisaient l'objet d'une entité spéciale du modèle. Celle-ci permet à la fois l'identification, l'authentification, la gestion de **rôles** associés à chaque utilisateur ainsi que celle des droits d'accès aux ressources de l'application.  

La sécurité est naturellement un problème bien plus vaste. Nous en avons mis en œuvre une petite partie avec les jetons CSRF qui protègent l'application contre l'envoi de formulaires « piégés ». Mais nous nous concentrons ici sur la question des utilisateurs.

Le premier aspect est la définition même des utilisateurs de l'application. Il existe diverses façons de mémoriser les personnes autorisées à accéder à des ressources. Dans bon nombre de cas, la source sera une base de données locale, mais Symfony autorise des alternatives comme des annuaires LDAP, voire des utilisateurs définis _ad hoc_ en mémoire.

Une partie importante, entièrement prise en charge par Symfony est l'identification et l'authentification des utilisateurs, que l'on peut encore subdiviser en deux volets :
- la création d'un nouveau profil au travers du processus d'enregistrement
- la connexion et la déconnexion à l'application, qui peut se faire, là aussi, de multiples manières, la plus courante étant un formulaire avec identifiant et mot de passe.

Une troisième partie concerne la gestion des droits d'accès en eux-mêmes. Là encore, plusieurs possibilités sont offertes, avec des granularités plus ou moins fines en fonction des besoins. La question posée est de savoir qui a le droit de faire quoi avec les ressources de l'application. La réponse se base sur l'analyse d'éléments de contexte, comme :
- la nature de la ressource à laquelle l'utilisateur veut accéder
- les rôles qui sont alloués à l'utilisateur
- des indicateurs d'infrastructure (adresse IP, port, etc.)
- d'autres critères arbitraires définis par les administrateurs

Une bonne partie de la mise en route de la sécurité des applications se fait de manière déclarative, soit dans le fichier de configuration `security.yaml`, soit dans le code de l'application via des annotations. Dans certains cas, on aura à développer des classes spécifiques, voire à intercepter le processus implicite d'identification et d'authentification de Symfony, mais nous nous limiterons pour le moment à des cas simples.  

## Notes de cours
- [Utilisateurs](1-Utilisateurs.md)
- [Identification/Authentification](2-Identification.md)
- [Contrôles d'accès simples](3-Contrôles.md)
- [Contrôles d'accès avec voteurs](4-Voteurs.md)
- [Personnalisation des pages d'erreurs](5-Personnalisation-Erreurs.md)

## Syntaxe
- [Aide-mémoire](Aide-mémoire.md)

## Ressources
- [Ressources documentaires](Ressources.md)
