<?php
// Connexion au site t411.me
function connexion_site($utilisateur, $mot_de_passe)
{
	$timeout = 10;
	$cookies_file = dirname(__FILE__)."/cookies.txt";
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
	'login' => $utilisateur,
	'password' => $mot_de_passe,
	));
	
	// Fichier dans lequel cURL va écrire les cookies
	// (pour y stocker les cookies de session)
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies_file);
	
	curl_exec($ch);
	curl_close($ch);
}

// Déconnexion du site et effacement du fichier cookies.txt
function deconnexion_site ()
{
	
	$url = 'http://www.t411.me/users/logout/';
	$ch = curl_init($url);
	
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	
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
	
	if (file_exists(dirname(__FILE__)."/cookies.txt"))
		unlink(dirname(__FILE__)."/cookies.txt");
}

// Récupère le ratio sur t411.me
function scrape_ratio($utilisateur,$mot_de_passe)
{
	// Requête de connexion
	connexion_site($utilisateur, $mot_de_passe);
	
	// Requête pour afficher la page d'acceuil
	$url = 'http://www.t411.me'; 
	$timeout = 10;
	$cookies_file = dirname(__FILE__)."/cookies.txt";
	
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
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file); 
	
	$page_content = curl_exec($ch); 
	
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	
	curl_close($ch);
	
	// Retourne la valeur du ratio sinon FALSE
	if ($http_code == 200) 
	{ 
		if (preg_match('#<span>Ratio: <strong class=\"rate\">(\d,\d\d)</strong>#', $page_content, $matches)) 
			return ($matches[1]); 
		else 
			return ($erreur = FALSE);
	}
	
	else 
	{ 
		return ($erreur = FALSE); 
	}
	
	// Requête de déconnexion
	deconnexion_site();
}

// Téléchargement d'un torrent (récupère automatiquement un torrent dans le top100)
function telechargement_torrent($utilisateur_php_ratio,$utilisateur, $mot_de_passe)
{
	// Requête de connexion
	connexion_site($utilisateur, $mot_de_passe);

	// Requête de récupération de la page top100 */
	$url = 'http://www.t411.me/top/100/';
	$timeout = 10;
	$cookies_file = dirname(__FILE__)."/cookies.txt";
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if (preg_match('`^https://`i', $url))
	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);

	$page_content = curl_exec($ch);

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	// Recherche du premier torrent top100
	if ($http_code == 200) 
	{
		
		if (preg_match('#<a href="/torrents/nfo/\?id=(.+?)" class="ajax nfo">#s', $page_content, $id_torrent_top100) AND preg_match('#<a href="http://www.t411.me/torrents/(.+?)" title=#s', $page_content, $nom_torrent_top100))
			{
			$chemin_download_torrent = "http://www.t411.me/torrents/download/?id=".$id_torrent_top100[1];
	
			$url  = $chemin_download_torrent;
			$path = 'utilisateurs/'.$utilisateur_php_ratio.'/'.$nom_torrent_top100[1].'.torrent';
	    	
			// Requête téléchargement torrent
			$fp = fopen($path, 'w');
			 
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			
			$page_content = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			 
			fclose($fp);
			
			if ($http_code == 200) {
				$torrent = new Torrent('utilisateurs/'.$utilisateur_php_ratio.'/'.$nom_torrent_top100[1].'.torrent');
				$torrents['hash'] = $torrent->hash_info();
				$announce_recupere = $torrent->announce();
				if (is_array($announce_recupere)) $torrents['announce'] = $announce_recupere['0']['0'];
				Else $torrents['announce'] = $announce_recupere;
				// On écrit un fichier ini contenant les infos du torrent
				$ini = new ini ('utilisateurs/'.$utilisateur_php_ratio.'/t411.me.ini', 'Informations du fichier torrent: '.$nom_torrent_top100[1].'.torrent');
				$ini->ajouter_array($torrents);
				$ini->ecrire();
				// On éfface le torrent
				unlink('utilisateurs/'.$utilisateur_php_ratio.'/'.$nom_torrent_top100[1].'.torrent');
				return ($erreur = TRUE);
			}
			else {
				return ($erreur = FALSE);
			}
			
			}
		else 
			{
			return ($erreur = FALSE);
			}
	}
	
	else
		{
			return ($erreur = FALSE);
		}

	// Requête de déconnexion
	deconnexion_site();
}
?>