<?php
/*
	Transmission de l'upload et du download au tracker 
*/

// --------------------- Librairie --------------------------------

// Importation de la librairie torrent.php
include("librairies/torrent.php");

// --------------------- Configuration ----------------------------

// Importation de la configuration (stockée dans le fichier configuration.ini)
$tableau_ini = parse_ini_file("configuration/configuration.ini");

// --------------------- Fonctions --------------------------------

// Fonction d'écriture d'un log sur l'écran et dans le fichier log.txt
function addLog($txt) {
	if (!file_exists("log.txt")) file_put_contents("log.txt", "");
	file_put_contents("log.txt",date("[j/m/y H:i:s]")." - $txt \r\n".file_get_contents("log.txt"));
	echo date("[j/m/y H:i:s]")." - $txt <br>";
 }

 // Fonction d'extraction des informations du torrent
 function extraction_torrent($chemin) {	
	// Création de l'objet torrent
	$torrent = new Torrent($chemin);
	
	// Récupération du hash
	$torrents['hash'] = $torrent->hash_info();
	
	// Récupération de announce (si plusieurs announce, conserver uniquement le premier)
	$announce_recupere = $torrent->announce();
	if (is_array($announce_recupere))
		$torrents['announce'] = $announce_recupere['0']['0'];
	Else
		$torrents['announce'] = $announce_recupere;
		
	return $torrents;
 }

// Fonction pour remplacer une valeur dans un ini (exemple: remplace_option_ini ("configuration/configuration.ini", "valeur mini download", "500");)
function remplace_option_ini ($fichier_ini, $option_recherche, $valeur_insere) {
	$fichier_parse_ini = parse_ini_file($fichier_ini);
	
	$fichier_parse_ini["$option_recherche"] = $valeur_insere;
	unlink($fichier_ini);
	
	foreach($fichier_parse_ini as $clef => $element)
	{
		$ecrire = $clef.' = "'.$element.'"'."\n";
		file_put_contents($fichier_ini, $ecrire, FILE_APPEND);
	}
}

// --------------------- Script --------------------------------------

addLog("Lancement du script"); 

// Tester si le quota demandé est atteint
if($tableau_ini['total de byte a uploader'] <= $tableau_ini['quantite transmise upload'] AND $tableau_ini['total de byte a downloader'] <= $tableau_ini['quantite transmise download']) {
	addLog("Quota atteint, fin du script");
	Exit;
	}

// Tester si le fichier existe et s'il est lisible
if(is_readable($tableau_ini['chemin du torrent']))
    addLog("Chemin du fichier correct = ".$tableau_ini['chemin du torrent']);
else {
	addLog("Chemin du fichier incorrect ou illisible.");
	exit;
	}

// Récupération des infos du tracker
$torrents = extraction_torrent($tableau_ini['chemin du torrent']);
addLog("Torrent Hash = ".$torrents['hash']." Announce = ".$torrents['announce']);
addLog("Announce = ".$torrents['announce']);

// Hash -> transformation en binaire et encodage en url
$torrents['hash_bin_url'] = urlencode( pack("H*" , $torrents['hash'] ) );
addLog("Hash en Binaire URL = ".$torrents['hash_bin_url']);

// Création d'un peer id en MD5 à l'aide de l'heure
$torrents['peer_id'] = $tableau_ini['user agent prefixe'].substr(md5(time().microtime()),0,12);
addLog("Identitée du Peer en MD5 = ".$torrents['peer_id']);

// Communication au tracker du début de la connection
addLog("Envoi de vos stats au tracker");

// Envoi de la commande start
$commande_started = $torrents['announce'].'?info_hash='.$torrents['hash_bin_url'].'&peer_id='.$torrents['peer_id'].'&event=started';
addLog("Envoi de la commande started = ".$commande_started);
$ch = curl_init();
curl_setopt($ch,CURLOPT_FRESH_CONNECT, true); 
curl_setopt($ch,CURLOPT_TIMEOUT, 15); 
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_USERAGENT,$tableau_ini['user agent']);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch,CURLOPT_URL,$commande_started);
$reponse_serveur = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
addLog("Code http de retour = ".$http_code);
	
if ($http_code != 200) { // Code 200 = requête correctement éxécutée
	addLog("Echec pour joindre le tracker");
	Exit;
	}
Else {
	addLog("Réponse = ".$reponse_serveur);
	}

// Génére une vitesse d'upload et de download entre les valeurs maxi et mini, regarde si quota dépassé pour upload ou download
if ($tableau_ini['total de byte a uploader'] <= $tableau_ini['quantite transmise upload']) {
	$uploaded = 0;
	$downloaded = mt_rand($tableau_ini['valeur mini download'], $tableau_ini['valeur maxi download']);
	}
else if ($tableau_ini['total de byte a downloader'] <= $tableau_ini['quantite transmise download']) {
	$downloaded = 0;
	$uploaded = mt_rand($tableau_ini['valeur mini upload'], $tableau_ini['valeur maxi upload']);
	}
else {
	$uploaded = mt_rand($tableau_ini['valeur mini upload'], $tableau_ini['valeur maxi upload']);
	$downloaded = mt_rand($tableau_ini['valeur mini download'], $tableau_ini['valeur maxi download']);
	}

// Envoi de la commande Stopped
$commande_stopped = $torrents['announce'].'?info_hash='.$torrents['hash_bin_url'].'&peer_id='.$torrents['peer_id'].'&uploaded='.$uploaded.'&downloaded='.$downloaded.'&event=stopped';
addLog("Envoi de la commande stopped = ".$commande_stopped);
$ch = curl_init();
curl_setopt($ch,CURLOPT_FRESH_CONNECT, true); 
curl_setopt($ch,CURLOPT_TIMEOUT, 15); 
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_USERAGENT,$tableau_ini['user agent']);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch,CURLOPT_URL,$commande_stopped);
$reponse_serveur = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
	
addLog("Code http de retour = ".$http_code);
	
if ($http_code != 200) { // Code 200 = requête correctement éxécutée
	addLog("Echec pour joindre le tracker");
	Exit;
	}
Else {
	addLog("Réponse = ".$reponse_serveur);
	}

remplace_option_ini ("configuration/configuration.ini", "quantite transmise upload", $tableau_ini['quantite transmise upload'] + $uploaded);
remplace_option_ini ("configuration/configuration.ini", "quantite transmise download", $tableau_ini['quantite transmise download'] + $downloaded);
addLog("Transmission de vos stats au tracker = OK!!!");
addLog("//----------------------------------------------------");
?>


