<?php
/*
Script pour modifier la configuration du logiciel
*/
// Importatioon de la configuration
$configuration_ini = parse_ini_file("configuration/configuration.ini");
$client_ini = parse_ini_file("configuration/client.ini", true);

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
							<br><label for="user_agent" required>Client bitorrent :</label>
								<select name="user_agent" id="user_agent">
									<?php liste_client_torrent(); ?>
								</select>
						</fieldset>
						<fieldset>
						<legend>Quota</legend>
							<label for="taille_byte_upload">Upload :</label>
								<?php echo $configuration_ini['quantite transmise upload']; ?> sur <input type="number" name="taille_byte_upload" id="taille_byte_upload" title="Taille en Byte" size="30" maxlength="30" value="<?php echo $configuration_ini['total de byte a uploader']; ?>" required/> (Byte)
				
							<br><label for="taille_byte_download">Download :</label>
								<?php echo $configuration_ini['quantite transmise download']; ?> sur <input type="number" name="taille_byte_download" id="taille_byte_download" title="Taille en Byte" size="30" maxlength="30" value="<?php echo $configuration_ini['total de byte a downloader']; ?>" required/> (Byte)
						</fieldset>

						<fieldset>
						<legend>Vitesse</legend>
							<label for="maxi_byte_upload">Maximum d'upload par envoi :</label>
								<input type="number" name="maxi_byte_upload" id="maxi_byte_upload" title="Taille en Byte" size="30" maxlength="30" value="<?php echo $configuration_ini['valeur maxi upload']; ?>" required/> (Byte)
				
							<br><label for="maxi_byte_download">Maximun de download par envoi :</label>
								<input type="number" name="maxi_byte_download" id="maxi_byte_download" title="Taille en Byte" size="30" maxlength="30" value="<?php echo $configuration_ini['valeur maxi download']; ?>" required/> (Byte)
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

