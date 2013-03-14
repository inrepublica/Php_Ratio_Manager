<?php
// Importation des librairies
include("librairies/log.php");

// Récupération de la variable $action GET ou POST
if (!empty($_GET['action'])) $action = $_GET['action'];
if (!empty($_POST['action'])) $action = $_POST['action'];

// Liste les commandes disponibles
if (!empty($action)) {
	if ($action == "voir_log") { voir_log($_SESSION['utilisateur']); }
	elseif ($action == "supprimer_log") { supprimer_log($_SESSION['utilisateur']); }
	else { echo "Mauvaise commande."; }
}
else {
	echo "Aucune commande dans l'URL.";
}
?>