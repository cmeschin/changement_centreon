# changement_centreon
Gestionnaire des changements de la supervision Centreon

La supervision de notre infrastructure est basée sur Centreon.
Nous avions besoin de formaliser et centraliser les demandes de changements à effectuer dans Centreon.
Cette interface a donc vu le jour dans cet objectif quels que soient les modifications à apporter à la supervision en place (Ajout, suppression, desactivation, modification).
C'est un projet qui m'a pris environ un an de développement pour avoir une première version utilisable.
Cela fait maintenant un peu plus d'un an qu'il est en place et qu'il évolue au fil du temps.

Les demandes sont regroupées par prestation (service rendu au client) correspondant aux "groupe de services" dans Centreon.
En effet la fourniture d'une prestation peut impacter un service sur un hôte, un hôte complet ou plusieurs services sur des hôtes différents.
Le Groupe de service était donc le moyen idéal d'effectuer ce regroupement.

Concernant le codage: je ne suis pas développeur. J'ai appris le développement php / mysql / javascript / jquery grâce aux cours d'openclassroom et sur mon temps libre. Merci à eux.
Il y a donc surement de grosses optimisations à faire dans le code et la gestion d'erreur n'est pas forcément au top :) .

Je m'améliore, enfin je crois :), au fil des pages de codes...
