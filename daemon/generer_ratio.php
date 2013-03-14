<?php
/*
 Transmission de données pour augmenter son ratio, utilisation de la tâche la plus ancienne.
*/

// Importation des Librairies
include("../librairies/ini.php");
include("../librairies/log.php");

// Importation des Configuration (fichiers *.ini)
$site_torrent_ini = parse_ini_file(dirname(__FILE__)."/../configuration/site_torrent.ini", true);
$client_ini = parse_ini_file(dirname(__FILE__)."/../configuration/client.ini", true);
$type_connection_ini = parse_ini_file(dirname(__FILE__)."/../configuration/type_connection.ini", true);

// Importation de la BDD
try {
	// Nouvel objet de base SQLite
	$bdd_handle = new PDO('sqlite:../bdd/db.sqlite');
	// Quelques options
	$bdd_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Recherche de la dernière tache
	$query = "SELECT * FROM taches ORDER BY timestamp_dernier_upload";
	$requete = $bdd_handle->prepare($query);
	$requete->execute();
	$resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
	// On charge le nom d'utilisateur avec le id_membre
	$query = "SELECT utilisateur FROM membres WHERE id = ?";
	$requete = $bdd_handle->prepare($query);
	$requete->execute(array($resultat[0]['id_membre']));
	$resultat2 = $requete->fetchAll(PDO::FETCH_ASSOC);
	$utilisateur_phpratiomanager = $resultat2[0]['utilisateur'];
	
	addLog($utilisateur_phpratiomanager, "||| Lancement du script de génération du ratio pour le site ".$resultat[0]['site']." |||", "non");
	include('../scraper/'.$site_torrent_ini[$resultat[0]['site']]['chemin scraper']);
	addLog($utilisateur_phpratiomanager, "Votre ratio = ".$resultat[0]['dernier_ratio'], "non");

	// Tester si le ratio est inférieur au ratio minimum
	if($resultat[0]['dernier_ratio'] > $resultat[0]['ratio_minimum']) {
		addLog($utilisateur_phpratiomanager, "Votre ratio est supérieur au ratio minimum (".$resultat[0]['ratio_minimum'].") , fin du script.", "non");
		addLog($utilisateur_phpratiomanager, "||| Fin du script |||", "non");
		exit;
	}
	
	// Tester si le torrent existe et s'il est lisible
	if(is_readable('../utilisateurs/'.$utilisateur_phpratiomanager.'/'.$resultat[0]['site'].'.ini'))
		addLog($utilisateur_phpratiomanager, "Chemin du fichier torrent correct", "non");
	else {
		addLog($utilisateur_phpratiomanager, "Chemin du fichier incorrect ou fichier illisible.", "oui");
		addLog($utilisateur_phpratiomanager, "||| Fin du script  |||", "non");
		exit;
	}
	
	// Récupération des infos du tracker
	$torrents = parse_ini_file('../utilisateurs/'.$utilisateur_phpratiomanager.'/'.$resultat[0]['site'].'.ini');
	addLog($utilisateur_phpratiomanager, "Torrent Hash = ".$torrents['hash']." Announce = ".$torrents['announce'], "non");
	
	// Hash -> transformation en binaire et encodage en url
	$torrents['hash_bin_url'] = urlencode( pack("H*" , $torrents['hash'] ) );
	addLog($utilisateur_phpratiomanager, "Hash en Binaire URL = ".$torrents['hash_bin_url'], "non");
	
	// Création d'un peer id en MD5 à l'aide de l'heure
	$torrents['peer_id'] = $client_ini[$resultat[0]['user_agent']]['user agent prefixe'].substr(md5(time().microtime()),0,12);
	addLog($utilisateur_phpratiomanager, "Identitée du Peer en MD5 = ".$torrents['peer_id'], "non");
	
	// Communication au tracker du début de la connection
	addLog($utilisateur_phpratiomanager, "Envoi de vos stats au tracker", "non");
	
	// Envoi de la commande start
	$commande_started = $torrents['announce'].'?info_hash='.$torrents['hash_bin_url'].'&peer_id='.$torrents['peer_id'].'&event=started';
	addLog($utilisateur_phpratiomanager, "Envoi de la commande started = ".$commande_started, "non");
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch,CURLOPT_TIMEOUT, 15);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_USERAGENT,$client_ini[$resultat[0]['user_agent']]['user agent']);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch,CURLOPT_URL,$commande_started);
	$reponse_serveur = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	addLog($utilisateur_phpratiomanager, "Code http de retour = ".$http_code, "non");
	
	if ($http_code != 200) { // Code 200 = requête correctement éxécutée
		addLog($utilisateur_phpratiomanager, "Echec pour joindre le tracker", "oui");
		addLog($utilisateur_phpratiomanager, "||| Fin du script  |||", "non");
		Exit;
	}
	Else {
		addLog($utilisateur_phpratiomanager, "Réponse = ".substr($reponse_serveur, 0, 50), "non");
	}
	
	// Génére une vitesse d'upload et de download
	$ecart_temps = time() - $resultat[0]['timestamp_dernier_upload']; // Ecart en seconde entre le dernier upload et maintenant
	$upload_max = $type_connection_ini[$resultat[0]['type_connection']]['maxi upload'];
	$download_max = $upload_max / 2;
	$uploaded = mt_rand($upload_max / 2, $upload_max) * $ecart_temps * 1000;
	$downloaded = mt_rand($download_max / 2, $download_max) * $ecart_temps * 1000;
	
	// Envoi de la commande Stopped
	$commande_stopped = $torrents['announce'].'?info_hash='.$torrents['hash_bin_url'].'&peer_id='.$torrents['peer_id'].'&uploaded='.$uploaded.'&downloaded='.$downloaded.'&event=stopped';
	addLog($utilisateur_phpratiomanager, "Envoi de la commande stopped = ".$commande_stopped, "non");
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch,CURLOPT_TIMEOUT, 15);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_USERAGENT,$client_ini[$resultat[0]['user_agent']]['user agent']);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch,CURLOPT_URL,$commande_stopped);
	$reponse_serveur = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	addLog($utilisateur_phpratiomanager, "Code http de retour = ".$http_code, "non");
	
	if ($http_code != 200) { // Code 200 = requête correctement éxécutée
		addLog($utilisateur_phpratiomanager, "Echec pour joindre le tracker", "oui");
		addLog($utilisateur_phpratiomanager, "||| Fin du script  |||", "non");
		Exit;
	}
	Else {
		addLog($utilisateur_phpratiomanager, "Transmission de vos stats au tracker = OK!!!", "non");
		// On prépare la requête
		$requete = $bdd_handle->prepare('UPDATE taches SET timestamp_dernier_upload= ? WHERE id= ?');
		// On l’éxécute.
		$maintenant = time();
		$requete->execute(array($maintenant, $resultat[0]['id']));
	}
	
	// On ferme la bdd
	$bdd_handle = NULL;
	}
	catch (Exception $e) {
		die(addLog($utilisateur_phpratiomanager, "Erreur de communication avec la bdd: ".$e->getMessage(), "non"));
	}
	
	addLog($utilisateur_phpratiomanager, "||| Fin du script  |||", "non");

?>