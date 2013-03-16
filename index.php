<?php
// Importation des librairies nécessaires
include ("librairies/gravatar.php");

// Fonction affichage statut membre
function affichage_statut_membre () {
	if (!empty($_SESSION['utilisateur'])) {
		echo '<a href="http://www.gravatar.com" target="_blank"><img src="'.$_SESSION['url_image_gravatar'].'" alt="Votre Gravatar" /></a>';
		echo $_SESSION['utilisateur'];
	}
}

// Initialise une session
session_start();

// Si session utilisateur est vide et cookies présent
if (empty($_SESSION['utilisateur']) AND !empty($_COOKIE['utilisateur']) AND !empty($_COOKIE['mot_de_passe'])) {
	// Recherche de l'utilisateur
	try {
		// Nouvel objet de base SQLite
		$bdd_handle = new PDO('sqlite:bdd/db.sqlite');
		// Quelques options
		$bdd_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// Recherche utilisateur mot de passe
		$query = "SELECT * FROM membres WHERE utilisateur=:utilisateur AND mot_de_passe=:mot_de_passe";
		$requete = $bdd_handle->prepare($query);
		$requete->execute(array('utilisateur' => $_COOKIE['utilisateur'], 'mot_de_passe' => $_COOKIE['mot_de_passe']));
		// On change la réponse SQL en réponse PHP.
		$resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
		// Si résultat positif on importe l'adresse mail et l'id du membre 
		if (!empty($resultat)) {
			$_SESSION['email'] = $resultat[0]['email'];
			$_SESSION['id'] = $resultat[0]['id'];
		}
		// On ferme la bdd
		$bdd_handle = NULL;
	
	} catch (Exception $e) {
		die('Erreur : '.$e->getMessage());
	}	
	
	// Si utlisateur est membre génére la session
	if (!empty($resultat)) {
		$_SESSION['utilisateur'] = $_COOKIE['utilisateur'];
		get_gravatar($_SESSION['email']);
	}
	// Si utilisateur non membre alors destruction des cookies
	else {
		setcookie('utilisateur', '', time() - 3600);
		setcookie('mot_de_passe', '', time() - 3600);
	}
}

// Créer les cookies et les paramètres de la session avec les paramètres $_POST de connexion.php si l'utilisateur est présent dans la base des membres
if (!empty($_POST['utilisateur']) AND !empty($_POST['mot_de_passe']) AND !empty($_POST['connexion'])) {
	// Recherche de l'utilisateur
	try {
		// Nouvel objet de base SQLite
		$bdd_handle = new PDO('sqlite:bdd/db.sqlite');
		// Quelques options
		$bdd_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// Recherche utilisateur mot de passe
		$query = "SELECT * FROM membres WHERE utilisateur=:utilisateur AND mot_de_passe=:mot_de_passe";
		$requete = $bdd_handle->prepare($query);
		$requete->execute(array('utilisateur' => $_POST['utilisateur'], 'mot_de_passe' => $_POST['mot_de_passe']));
		// On change la réponse SQL en réponse PHP.
		$resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
		// Si résultat positif on importe l'adresse mail et l'id du membre
		if (!empty($resultat)) {
			$_SESSION['email'] = $resultat[0]['email'];
			$_SESSION['id'] = $resultat[0]['id'];
		}	
		// On ferme la bdd
		$bdd_handle = NULL;
	
	} catch (Exception $e) {
		die('Erreur : '.$e->getMessage());
	}
	// Si utlisateur est membre génére la session et les cookies
	if (!empty($resultat)) {
		setcookie('utilisateur', $_POST['utilisateur'], time() + 365*24*3600, null, null, false, true);
		setcookie('mot_de_passe', $_POST['mot_de_passe'], time() + 365*24*3600, null, null, false, true);
		$_SESSION['utilisateur'] = $_POST['utilisateur'];
		get_gravatar($_SESSION['email']);
		header('Location: index.php');
	}
	// Mauvais utilisateur
	else {
		$utilisateur_faux = TRUE;
	}
}

// Suppression des cookies et de la session si demande de deconnexion
if (!empty($_GET['page']) AND $_GET['page'] == 'deconnexion') {
	setcookie('utilisateur', '', time() - 3600);
	setcookie('mot_de_passe', '', time() - 3600);
	session_destroy();
}

// Entête de page
include ('themes/entete.html');

// Détection de la présence d'une session valide
if (empty($_SESSION['utilisateur'])) {
	// Si l'utilisateur est un faux
	if (!empty($utilisateur_faux)) echo "Nom d'utilisateur ou mot de passe incorrect.";
	else include('contenu/connexion.php');
}
else {
	// Récupération de la variable $page GET ou POST
	if (!empty($_GET['page'])) $page = $_GET['page'];
	if (!empty($_POST['page'])) $page = $_POST['page'];
	// choix des pages
	if (!empty($page)) {
		switch ($page)
	 	{
	 		case "acceuil";
	 		include ('contenu/acceuil.php');
	 		break;
	 		
	 		case "configuration";
	 		include('contenu/configuration.php');
	 		break;
	 		
	 		case "taches";
	 		include ('contenu/taches.php');
	 		break;
	 		
	 		case "log";
	 		include('contenu/log.php');
	 		break;
	 		
	 		case "aide";
	 		include('contenu/aide.php');
	 		break;
	 			
	 		case "a_propos";
	 		include('contenu/a_propos.php');
	 		break;
	 		
	 		case "deconnexion";
	 		echo "Vous êtes maintenant déconnecté du site.";
	 		break;
	 		
	 		default:
	 		echo "La page demandé est inconnue.";
	 	}
	}
	else {
		include('contenu/acceuil.php');
	}
}

// Pied de page
include ('themes/piedpage.html');
?>