# projet-astronomie

## Que voulez-vous faire ?

### Description

J'aimerais mettre en place un site ludique qui permettrait aux fans d'astronomie comme aux néophytes de contribuer à la diffusion des connaissances de ce domaine et de les découvrir.

Le site se déclinerait sous forme d'un canevas¹ interactif représentant l'espace sur lequel l'utilisateur pourra se déplacer, quelle que soit la direction, via un PointerLock². Lors de sa navigation, il pourra découvrir les contributions de la communauté, autrement dit les astres (planètes, étoiles, satellites, ...), et interagir avec :

consulter un astre précis : titre, description, image / photo, contributeur, commentaires / réactions de la communauté, etc ;
créer un nouvel astre : remplir les champs des caractéristiques décrites ci-dessus ;
commenter un astre : raconter une anecdote, donner son avis, apporter des clarifications éventuelles (sur l'astre !) et liker ;
trier les astres : en fonction de leur type (planètes, étoiles...), des contributeurs (tous les astres créés par un tel), du nombre de likes reçus par la communauté, etc ;
et bien d'autres choses !
¹ Canevas : https://developer.mozilla.org/fr/docs/Web/HTML/Element/canvas
² PointerLock : https://developer.mozilla.org/fr/docs/WebAPI/Pointer_Lock

### MVP :

Authentification : inscription, connexion, consultation et édition du profil
Mise en place du canevas : déplacements (haut, bas, droite, gauche) et interactions (zoom, dézoom)
API : CRUD des astres (ajout, affichage et édition, la suppression étant réservée à la modération)
Back office : gestion des utilisateurs et de leurs contributions
V2 :

Back office approfondi : gestion des rôles (rôle de modérateur), statistiques (nombre de visites, tendances, ...)
Barre de navigation : ajout de filtres sur les astres, les utilisateurs, etc
Barre de recherche : selon les filtres de la barre de navigation
Système de grades : en fonction du degré de contribution des utilisateurs (nombre d'astres créés, de réactions / commentaires laissés, ...)
V3 :

Blog d'articles : nouvelles découvertes, le matériel conseillé, etc
Forum communautaire : avec topics sur divers sujets de l'astronomie
Système d'élections de l'astre et du contributeur du mois
Système de notifications : mailing, etc
V4 :

Création de nouvelles galaxies avec leurs canevas dédiés
Messagerie personnelle (nodeJS ?)
Opportunités
Projet ambitieux, créatif et évolutif
Problématiques techniques de taille
Mise en relation d'un site front et d'une API voire d'API externes
Côté back : API, Fullstack (Sessions, Twig, Doctrine, Security, Services, ...)
Côté front : Hooks, Components, Redux, Axios, SyntheticEvents, etc