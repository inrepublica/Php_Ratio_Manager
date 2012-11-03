<?php
/*
Script pour modifier la configuration du logiciel
*/

// Importatioon de la configuration
$configuration_ini = parse_ini_file("configuration/configuration.ini");
$client_ini = parse_ini_file("configuration/client.ini", true);
$type_connection_ini = parse_ini_file("configuration/type_connection.ini", true);
$site_torrent_ini = parse_ini_file("configuration/site_torrent.ini", true);


// Liste les fichiers torrent présent
function liste_torrent()
 {
	$repertoire = glob("torrent/*.torrent");
	global $configuration_ini;
	// if (!empty($repertoire)) {
		foreach ($repertoire as $repertoire) {
			$fichier = substr($repertoire, 8);
			if ($configuration_ini['chemin du torrent'] == $repertoire) {
				echo "<option value=\"$repertoire\" selected>$fichier</option>";
			}
			else {
				echo "<option value=\"$repertoire\">$fichier</option>";
			}
		}
	// }
 }
 
// Liste les client torrent possible
function liste_client_torrent()
 {
	global $configuration_ini;
	global $client_ini;	
	foreach($client_ini as $clef => $element) {
		echo $clef;
		if ($configuration_ini['user agent'] == $element['user agent']) {  // Détection de la précedente configuration
			echo "<option value=\"$clef\" selected>$clef</option>";
		}
		else {
			echo "<option value=\"$clef\">$clef</option>";
		}
	}
 }
 
 // Liste les type de connection
function liste_type_connection()
 {
	global $configuration_ini;
	global $type_connection_ini;	
	foreach($type_connection_ini as $clef => $element) {
		echo $clef;
		if ($configuration_ini['type connection'] == $clef) {  // Détection de la précedente configuration
			echo "<option value=\"$clef\" selected>$clef</option>";
		}
		else {
			echo "<option value=\"$clef\">$clef</option>";
		}
	}
 }
 
 // Liste les différents sites de torrent
function liste_site_torrent()
 {
	global $configuration_ini;
	global $site_torrent_ini;	
	foreach($site_torrent_ini as $clef => $element) {
		echo $clef;
		if ($configuration_ini['site torrent'] == $clef) {  // Détection de la précedente configuration
			echo "<option value=\"$clef\" selected>$clef</option>";
		}
		else {
			echo "<option value=\"$clef\">$clef</option>";
		}
	}
 }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Configuration de PHP Ratio Manager</title>
    </head>

    <body>
		<form method="post" action="commandes.php">
			<table border="0">
				<tr>
					<td>
						<fieldset>
						<legend>Torrent</legend>
							<label for="torrent" required>Torrent à utiliser :</label>
								<select name="torrent" id="torrent">
									<?php liste_torrent(); ?>
								</select>
								&nbsp&nbsp<a href="gestion_fichiers_torrent.php?action=ajouter_fichier_torrent"><img src="images/upload.png" alt="Ajouter un torrent" width="20" height="20" title="Ajouter un torrent"></a>
								&nbsp&nbsp<a href="gestion_fichiers_torrent.php?action=suprimer_fichier_torrent"><img src="images/suprimer.jpg" alt="Suprimer un torrent" width="20" height="20" title="Suprimer un torrent"></a>
							<br><label for="user_agent" required>Client bitorrent :</label>
								<select name="user_agent" id="user_agent">
									<?php liste_client_torrent(); ?>
								</select>
						</fieldset>
						
						<fieldset>
							<legend>Ratio</legend>
								<label for="site_torrent" required>Site torrent à utiliser :</label>
									<select name="site_torrent" id="site_torrent">
										<?php liste_site_torrent(); ?>
									</select>
									&nbsp&nbsp Configurer mes identifiants : <input type="checkbox" name="configurer_site_torrent" value="oui">
								<br>
									<label for="ratio_minimum">Maintenir mon ratio à :</label>
										<input type="number" name="ratio_minimum" id="ratio_minimum" title="Ratio minimum" size="3" maxlength="1" value="<?php echo $configuration_ini['ratio minimum']; ?>" required/> Minimum
								<br>
								Ratio actuel : <?php echo $configuration_ini['dernier ratio connu']; ?>		
						</fieldset>

						<fieldset>
						<legend>Connection</legend>
							<label for="type_connection" required>Type de connection :</label>
								<select name="type_connection" id="type_connection">
									<?php liste_type_connection(); ?>
								</select>
						</fieldset>
			
			<input type="hidden" name="action" value="sauvegarde_configuration_ini">
			<input type="submit" value="Appliquer" />
		</form>
		<form method="post" action="commandes.php">
			<input type="hidden" name="action" value="remise_configuration_origine">
			<input type="submit" value="Paramètres par défaut" />
		</form>
					</td>
					<td>
						<fieldset>
							<legend>Fichier Log</legend>
								<form method="post" action="commandes.php">
									<input type="hidden" name="action" value="efface_log">
									<input type="submit" value="Supprimer" />
					
								</form>
								<form method="post" action="commandes.php">
									<input type="hidden" name="action" value="voir_log">
									<input type="submit" value="Voir" />
								</form>	
						</fieldset>
						<a href="credit.html" title="A propos" target="_blank"><img border="0" src="images/logo.png" alt="Php Ratio Manager"></a>
					</td>
				</tr>
			</table>
    </body>

</html>

