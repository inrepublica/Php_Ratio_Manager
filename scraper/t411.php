<?php 

function scrape_ratio()
{

// Récupération du username et password
$site_torrent_ini = parse_ini_file(__DIR__."/../configuration/site_torrent.ini", true);
$alsa_username = $site_torrent_ini['t411.me']['utilisateur']; 
$alsa_password = $site_torrent_ini['t411.me']['mot de passe']; 

$timeout = 10; 

$cookies_file = __DIR__."/cookies.txt"; 

/************************************************** 
Première requête : Connexion 
**************************************************/ 

$url = 'http://www.t411.me/users/login'; 

$ch = curl_init($url); 

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); 
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 

if (preg_match('`^https://`i', $url)) 
{ 
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
} 

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_NOBODY, true); 

// Forcer cURL à utiliser un nouveau cookie de session 
curl_setopt($ch, CURLOPT_COOKIESESSION, true); 

curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_POSTFIELDS, array( 
  'login' => $alsa_username, 
  'password' => $alsa_password, 
)); 

// Fichier dans lequel cURL va écrire les cookies 
// (pour y stocker les cookies de session) 
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies_file); 

curl_exec($ch); 

curl_close($ch); 

/************************************************** 
Seconde requête : Récupération du contenu 
**************************************************/ 

$url = 'http://www.t411.me'; 

$ch = curl_init($url); 

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); 
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 

if (preg_match('`^https://`i', $url)) 
{ 
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
} 

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_COOKIESESSION, true); 

// Fichier dans lequel cURL va lire les cookies 
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file); 

$page_content = curl_exec($ch); 

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

curl_close($ch); 

/************************************************** 
Troisème requête : Déconnexion 
**************************************************/ 

$url = 'http://www.alsacreations.com/ident/logout/'; 

$ch = curl_init($url); 

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); 
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 

if (preg_match('`^https://`i', $url)) 
{ 
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
} 

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_NOBODY, true); 
curl_setopt($ch, CURLOPT_COOKIESESSION, true); 

curl_exec($ch); 

curl_close($ch); 

// Effacement du fichier de stockage des cookies 

if (file_exists($cookies_file)) 
  unlink($cookies_file); 

/**************************************** 
Retourner la valeur du ratio sinon FALSE
****************************************/ 

if ($http_code == 200) 
{ 
	if (preg_match('#<span>Ratio: <strong class=\"rate\">(\d,\d\d)</strong>#', $page_content, $matches)) 
		return ($matches[1]); 
	else 
		return $matches = FALSE;
}

else 
{ 
	return $maches = FALSE; 
}

}
?>