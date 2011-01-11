# 123PeopleRemover proxy #

123People ayant bloqué l'adresse IP de mon serveur, je propose à la communauté de participer au projet. 

En effet, seul le processus de récupération de la page sur 123People est bloqué. En changeant de serveur, 123People aurait juste eu besoin de bloquer la nouvelle IP. 
J'ai donc choisi la solution des "proxy" pour contrer ce problème. Ceci afin de permettre aux utilisateurs finaux de pouvoir utiliser le service sans ce soucier d'aucune considération technique. 

## Installation ##
Pré-requis: PHP5 >= 5.2.0 (à cause de json_encode)
Les serveurs .free.fr ne sont pas compatibles.

### Utilisateurs débutants ###
* Télécharger le projet (clic sur bouton plus haut dans la page) sur votre ordinateur
* Décompresser puis envoyer le dossier (peut-importe son nom) sur un ou plusieurs de vos serveurs (via ftp ou autre)
* Tester le script (explication ci-dessous)
* (temporaire) Envoyer un mail à 123PeopleRemover[_at_]fgribreau.com avec les urls où se trouve le script

### Utilisateurs confirmés ###

* Cloner git sur un ou plusieurs de vos serveurs
* Tester le script (explication ci-dessous)
* (temporaire) Envoyer un mail à 123PeopleRemover[_at_]fgribreau.com avec les urls où se trouve le script

PS: Si vous souhaitez envoyer le script sur plusieurs serveurs vérifiez qu'ils diffèrent de par leurs IP.

### (Etape 1) Tester le script (état par défaut) ###

Une fois installé sur votre serveur, allez à l'adresse __http://votreServeur.com/dossier123PeopleRemover/__
Le script devrait afficher:
	defaultCallback({"version":1,"content":{"errId":0,"errMsg":"No parameter \"callback\" specified"}});

### (Etape 2) Tester le script - récupération d'une page 123People ###

Ajoutez à l'adresse __http://votreServeur.com/dossier123PeopleRemover/__ le texte suivant (remplacez [url_123PEOPLE] par l'url d'une page 123People que <a href="http://www.albionresearch.com/misc/urlencode.php">vous aurez préalablement encodée</a>):
	?callback=cb&url=[url_123PEOPLE]

Le script devrait afficher du texte (json) commençant par:
	cb({"version":1,"content":{"links":[

Dans le cas contraire, il est fort probable que la version de PHP soit trop ancienne pour exécuter le script.

## Considérations techniques ##

D'avance désolé pour la qualité du code, j'ai juste eu le temps de le re-factoriser en vitesse. 
N'hésitez pas à forker le projet !