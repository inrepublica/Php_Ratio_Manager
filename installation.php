<?php
/*
Script d'installation pour php ratio manager
- Créer un fichier .htaccess
- Créer un fichier .htpasswd
*/

// Fonction installation (création fichier .htaccess et .htpasswd
function installation() {
	$OS = substr(php_uname('s'), 0, 3);
	if ($OS == "Win") { // Installation sur un système WINDOWS (mot de passe en clair)
		$chemin_htaccess = __DIR__."/.htaccess";
		$chemin_htpasswd = __DIR__."/.htpasswd";
		$contenu_htaccess = "AuthName \"PHP Ratio Manager\"\nAuthType Basic\nAuthUserFile \"$chemin_htpasswd\"\nRequire valid-user";
		file_put_contents($chemin_htaccess, $contenu_htaccess);
		$contenu_htpasswd = $_POST['identifiant'].":".$_POST['mot_de_passe'];
		file_put_contents(__DIR__.'/.htpasswd', $contenu_htpasswd);
	}
	else { // Installation sur un autre système (mot de passe chiffré)
		$chemin_htaccess = __DIR__."/.htaccess";
		$chemin_htpasswd = __DIR__."/.htpasswd";
		$contenu_htaccess = "AuthName \"PHP Ratio Manager\"\nAuthType Basic\nAuthUserFile \"$chemin_htpasswd\"\nRequire valid-user";
		file_put_contents($chemin_htaccess, $contenu_htaccess);
		$contenu_htpasswd = $_POST['identifiant'].":".crypt($_POST['mot_de_passe']);
		file_put_contents(__DIR__.'/.htpasswd', $contenu_htpasswd);
	}
	echo "Identifiant et mot de passe enregistrés.<br><a href='index.php'>Configurer Php Ratio Manager.</a>";
}

// Liste les commandes disponibles
if (!empty($_POST)) {
	if ($_POST['action'] == "installation") { installation(); }
	else { echo "Mauvaise commande POST.<br><a href='index.php'>Retour</a>"; }
 }
else {
	echo '
	<form action="installation.php" method="post">
		<p>
			Merci de fournir un identifiant / mot de passe pour protéger Php Ratio Manager :<br />
			<label for="identifiant">Identifiant :</label>
			<input type="text" name="identifiant" id="identifiant" title="identifiant" size="15" maxlength="50"><br>
			<label for="mot_de_passe">Mot de passe :</label>
			<input type="password" name="mot de passe" id="mot_de_passe" title="Mot de passe" size="15" maxlength="50"><br>
			<input type="hidden" name="action" value="installation">
			<input type="submit" value="Appliquer" />
		</p>
	</form>
	<br><a href="index.php">Retour</a>
';
}
?>