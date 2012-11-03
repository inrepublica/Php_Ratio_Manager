-----------------
Php Ratio Manager
-----------------


1.Licence:
----------
Les sources de ce logiciel sont publiées sous la License GPL v3. Merci de vous rendre: http://www.gnu.org/licenses/gpl.html pour obtenir une copie de la licence.


2.Prérequis:
------------
- PHP 5.2 ou supérieur avec l'extension CURL activé
- Avoir accès au Daemon CRON ou utiliser un service de CRON par internet (ex: http://www.cronjobonline.com/ )


3.Fonctionnalités et fonctionnement:
------------------------------------
Ce logiciel sert a maintenir votre ratio de download/upload sur un tracker privé (Bittorrent). Une fois configuré à l'aide de la page "index.php", la page "transmission_serveur.php"
va informer le tracker de vos statistiques de download et upload. Cela va se faire en plusieurs envois, chaque envoi générant une vitesse aléatoire (upload et download) entre le paramètre maximum et la moitié du paramètre maximum.
Une le ratio minimum atteint, le script ne génére plus d'envoi.


4.Installation:
---------------
- Copiez l'intégralité de l'archive dans le répertoire de votre choix sur votre serveur.
- Lancer via un navigateur (firefox par exemple) la page "php_ratio_manager/installation.php"
- !!!ATTENTION!!! -> Une fois l'installation terminée, n'oubliez pas de supprimer le fichier installation.php
- Ajouter une tâche CRON pour lancer automatiquement le scrpit "daemon.php" à intervalle régulière. Un délai de 30 minutes entre deux passes est un bon choix.
	Exemple: "*/30    *       *       *       *       root    /usr/bin/php /volume1/web/php_ratio_manager/daemon.php"
- Si vous n'avez pas accès au daemon cron, vous pouvez un service de cron à distance (ex: http://www.cronjobonline.com/ )
- En cas de perte de votre identifiant/mot de passe effacer les fichiers .htaccess et .htpasswd, puis les regénérer en appelant la page installation.php

5.Configuration:
----------------
- Lancer via un navigateur (firefox par exemple) la page "php_ratio_manager/index.php"
Explication des options:
	- Torrent:
		- Torrent à utiliser: Liste des torrents disponible
		- Icône Ajouter un torrent: Vous permet de choisir un torrent sur votre ordinateur
		- Icône Suprimer un torrent: Vous permet de suprimer un torrent devenu inutile
		- Client bitorrent: Sélectionner le client bitorrent à émuler
	- Ratio:
		- Site torrent à utiliser: Vous permet de choisir le site de torrent privé a utiliser
			- Cocher la case Configurer mes identifiants avant de faire appliquer, pour pouvoir modifier l'utilisateur / mot de passe du site torrent à utiliser
		- Maintenir mon ratio à: Définit le ratio minimum à atteindre
		- Ratio actuel: Rappel de votre taux de ratio sur le site de torrent privé sélectionné. Lors de la première utilisation cette information est vide
	- Connection:
		- Type de connection: Vous permet de choisir votre type de connection (ADSL/ADSL2/Fibre optique). Ce paramètre modifie les vitesses maxi et mini pour l'upload/download.
	- Appliquer: Permet de sauvegarder la configuration
	- Paramètres par défaut: Remet la configuration initiale
	- Log:
		- Voir: Permet d'afficher les logs du script
		- Supprimer: Vide le fichier log
	- Icône Php Ratio Master: Information sur le logiciel


6.Remerciement:
---------------
- Adrien Gibrat pour ca bibliothèque PHP torrent-rw: https://github.com/adriengibrat/torrent-rw
- Pavel InFeRnODeMoN pour le logo: http://kde-look.org/usermanager/search.php?username=InFeRnODeMoN

7.Changelog:
------------
v0.3:
- Disparition du paramètrage manuel, ajout d'un système de gestion du ratio automatique grâce au ratio fournit par le site de torrent privé.
- Support du site de torrent privé t411.me -> http://www.t411.me

v0.2:
- Remplacement des paramètres maxi et mini pour le download/upload par le choix d'un type de connection (ADSL/ADLS2/Fibre optique)

v0.1:
- Première version du script