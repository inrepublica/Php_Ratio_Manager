<?php
/*
Gestion des fichiers torrent sur le serveur. Ajout et suppression.
*/

// Formulaire pour l'envoi d'un fichier torrent
function ajouter_fichier_torrent()
{
echo '
	<form action="gestion_fichiers_torrent.php" method="post" enctype="multipart/form-data">
        <p>
                Fichier torrent à mettre sur le serveur :<br />
				<input type="hidden" name="action" value="ajout_fichier_torrent">
                <input type="file" name="fichier" /><br />
                <input type="submit" value="Envoyer le fichier" />
        </p>
	</form>
';
echo "<br><a href='index.php'>Retour</a>";
}

// Ajout d'un fichier torrent sur le serveur
function ajout_fichier_torrent()
{
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
if (isset($_FILES['fichier']) AND $_FILES['fichier']['error'] == 0)
	{
        // Testons si le fichier n'est pas trop gros
        if ($_FILES['fichier']['size'] <= 1000000)
        {
                // Testons si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['fichier']['name']);
                $extension_upload = $infosfichier['extension'];
                $extensions_autorisees = array('torrent'); // Choix des extensions valides
                if (in_array($extension_upload, $extensions_autorisees))
                {
                        // On peut valider le fichier et le stocker définitivement
                        move_uploaded_file($_FILES['fichier']['tmp_name'], 'torrent/' . basename($_FILES['fichier']['name']));
                        echo "L'envoi a bien été effectué !<br><a href='index.php'>Retour</a>";
                }
        }
	}
}

// Formulaire pour la suppression d'un torrent sur le serveur
function suprimer_fichier_torrent()
{
	$repertoire = glob(__DIR__."/torrent/*.torrent");
	if (!empty($repertoire)) {
		echo '<form action="gestion_fichiers_torrent.php" method="post" enctype="multipart/form-data">
				<p>
                Fichier torrent à éffacer :<br />
					<select name="liste_torrent" id="liste_torrent">';
					foreach ($repertoire as $repertoire) {
						$fichier = basename($repertoire);
					echo "<option value=\"$repertoire\">$fichier</option>";
	echo '			</select>
					<input type="hidden" name="action" value="suppression_fichier_torrent">
					<br><input type="submit" value="Appliquer" />
				</p>
			</form>';
	echo "<br><a href='index.php'>Retour</a>";
		}
	}
	else {
		echo "Pas de fichier Torrent!<br><a href='index.php'>Retour</a>";
	}
}

// Suppression d'un fichier torrent sur le serveur
function suppression_fichier_torrent()
{
unlink($_POST['liste_torrent']);
echo "Fichier torrent éffacé.<br><a href='index.php'>Retour</a>";
}

// Liste les commandes disponibles
if (!empty($_GET)) {
	if ($_GET['action'] == "ajouter_fichier_torrent") { ajouter_fichier_torrent(); }
	elseif ($_GET['action'] == "suprimer_fichier_torrent") { suprimer_fichier_torrent(); }
	else { echo "Mauvaise commande GET.<br><a href='index.php'>Retour</a>"; }
}
elseif (!empty($_POST)) {
	if ($_POST['action'] == "ajout_fichier_torrent") { ajout_fichier_torrent(); }
	elseif ($_POST['action'] == "suppression_fichier_torrent") { suppression_fichier_torrent(); }
	else { echo "Mauvaise commande POST.<br><a href='index.php'>Retour</a>"; }
}
else {
	echo "Aucune commande dans l'URL.<br><a href='index.php'>Retour</a>";
}
?>