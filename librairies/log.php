<?php
// Format de log: Date Heure # Message # Erreur oui ou non

// Fonction d'écriture d'un log sur l'écran et dans le fichier log.txt
function addLog($utilisateur, $txt, $erreur) {
	if (file_get_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt") == "") { $contenu_log = date("j/m/y H:i:s")."#".$txt."#".$erreur; } // Si fichier vide pas de \n
	else { $contenu_log = "\n".date("j/m/y H:i:s")."#".$txt."#".$erreur; }

	
	// Limite de taille pour le fichier log
	$limite = 100000;
	if(filesize(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt") > $limite){
		$content = file_get_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt");
		$explode = explode("\n",$content);
		unset($explode[0]);
		$newContent = implode("\n",$explode);
		file_put_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt", $newContent.$contenu_log);
	}
	else {
		file_put_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt", $contenu_log, FILE_APPEND);
	}
	
	// Affichage sur l'écran
	if ($erreur == "oui") {
		echo "<font color=\"red\">".date("[j/m/y H:i:s]")." - $txt </font><br>";
	}
	else {
		echo date("[j/m/y H:i:s]")." - $txt <br>";
	}
 }
 
 // Fonction pour visionner le log
function voir_log($utilisateur) {
	if (($handle = fopen(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt", "r")) !== FALSE) {
		if (file_get_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt") == "") { echo "Fichier log vide."; }
		else {
		
			// Recupere le contenu du log dans un tableau
			$i = 0;
			while (($data = fgetcsv($handle, 2000, "#")) !== FALSE) {
				$tableau_csv[$i] = $data;
				$i++;
			}
			fclose($handle);
			// Affiche le tableau dans l'ordre décroissant
			$i = count($tableau_csv)-1;
			while ($i >= 0) {
				if ($tableau_csv[$i][2] == "oui") {
					echo '<font color="red">['.$tableau_csv[$i][0].'] - '.$tableau_csv[$i][1].'</font><br>';
				}
				else {
					echo '['.$tableau_csv[$i][0].'] - '.$tableau_csv[$i][1].'<br>';
				}
				$i--;
			}
		}
	}
	else { echo "Fichier log absent."; }
 }

// Fonction d'éffacement du log
function supprimer_log($utilisateur)
 {
	file_put_contents(dirname(__FILE__)."/../utilisateurs/".$utilisateur."/log.txt", '');
	echo "Fichier log éffacé";
 }
?>