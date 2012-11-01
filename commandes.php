<?php
/*
	Fichier pour éxécuter les commandes
*/

// Fonction pour remettre les paramètres par défaut
function remise_configuration_origine() {
	unlink("configuration/configuration.ini");
	copy("configuration/configuration.ini.defaut", "configuration/configuration.ini");
	echo "Remise des paramètres par défaut";
	echo "<br><a href='index.php'>Retour</a>";
 }

// Fonction pour sauvegarder la configuration dans configuration.ini
function sauvegarde_configuration_ini() {
	$client_ini = parse_ini_file("configuration/client.ini", true);
	$type_connection_ini = parse_ini_file("configuration/type_connection.ini", true);
	if (empty($_POST['torrent'])) $_POST['torrent'] = ""; // Vérification si pas de fichier torrent
	$nouvelle_configuration = array (
		'chemin du torrent' => $_POST['torrent'],
		'user agent' => $nouveau_user_agent = $client_ini[$_POST['user_agent']]['user agent'],
		'user agent prefixe' => $nouveau_user_agent_prefixe = $client_ini[$_POST['user_agent']]['user agent prefixe'],
		'total de byte a uploader' => $_POST['taille_byte_upload'] * 1024 * 1024,
		'total de byte a downloader' => $_POST['taille_byte_download'] * 1024 * 1024,
		'quantite transmise upload' => "0",
		'quantite transmise download' => "0",
		'type connection' => $_POST['type_connection'],
		'valeur maxi upload' => $nouveau_maxi_upload = $type_connection_ini[$_POST['type_connection']]['valeur maxi upload'],
		'valeur mini upload' => $nouveau_mini_upload = $type_connection_ini[$_POST['type_connection']]['valeur mini upload'],
		'valeur maxi download' => $nouveau_maxi_download = $type_connection_ini[$_POST['type_connection']]['valeur maxi download'],
		'valeur mini download' => $nouveau_mini_download = $type_connection_ini[$_POST['type_connection']]['valeur mini download']
	);
	unlink("configuration/configuration.ini");
	
	foreach($nouvelle_configuration as $clef => $element)
	{
		$ecrire = $clef.' = "'.$element.'"'."\n";
		file_put_contents("configuration/configuration.ini", $ecrire, FILE_APPEND);
	}
	echo "Configuration sauvegardée";
	echo "<br><a href='index.php'>Retour</a>";
 }

// Fonction pour visionner le log
function voir_log() {
	if (!file_exists("log.txt")) {
		echo "Fichier log vide";
		echo "<br><a href='index.php'>Retour</a>";
		Exit; 
	}
	$contenu_log = file_get_contents('log.txt');
	echo '<textarea rows="20" cols="100">' . $contenu_log . '</textarea>';
	echo "<br><a href='index.php'>Retour</a>";
 }

// Fonction d'éffacement du log
function efface_log()
 {
	if (!file_exists("log.txt")) {
		echo "Fichier log déjà vide";
		echo "<br><a href='index.php'>Retour</a>";
		Exit; }
	unlink("log.txt");
	echo "Fichier log éffacé";
	echo "<br><a href='index.php'>Retour</a>";
 }

 // Si commande vide
if (empty($_POST['action'])) {
	echo "Aucune commande dans l'URL";
	echo "<br><a href='index.php'>Retour</a>";
	Exit; }
	
// Liste les commandes disponibles
switch ($_POST['action'])
 { 
    case "remise_configuration_origine";
		remise_configuration_origine();
	break;
	
	case "sauvegarde_configuration_ini":
		sauvegarde_configuration_ini();
	break;
	
	case "efface_log":
        efface_log();
    break;
    
	case "voir_log":
		voir_log();
	break;
	
    default:
        echo "Commande inconnu";
		echo "<br><a href='index.php'>Retour</a>";
 }
 
?>