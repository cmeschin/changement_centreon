<!DOCTYPE html>
<html>
<head>
<?php
	if (session_id()=='')
	{
	session_start();
	};
	include('top.php');
	include('log.php'); // chargement de la fonction de log
	addlog("Chargement de la page supervision!");
	/**
	 * Initialisation des constantes
	 */
	$_SESSION['Reprise'] = false;
	$_SESSION['Nouveau'] = false;
	$_SESSION['PDF'] = false;
	$date_demande=date("d-m-Y H:i:s");
	if ((isset($_SESSION['groupe_changement_centreon'])) && ($_SESSION['groupe_changement_centreon']=="GG_DEMANDECENTREON_ADMIN")) 
	{
	        $_SESSION['Admin']=True;
	}else {
	        $_SESSION['Admin']=False;
	};
	include_once('head.php');
	echo '</head>';
echo '<body>';
echo '<div id="principal">';
	echo '<header id="en-tete">';
	if (file_exists('maintenance.php')==False)
	{
		include_once('menu.php');
	};
	echo '</header>';
	echo '<section>';
		echo '<p>Bonjour ';
		 if(isset($_SESSION['groupe_changement_centreon']))
		{ echo $_SESSION['name_changement_centreon'];} else{ echo '';};
		echo ',';
		echo '<br />';
		echo 'nous sommes le ' . $date_demande;
		echo '<br />';
		if	(file_exists('maintenance.php')==False)
		{ 
			echo 'Merci de choisir un menu ci dessus!</p>';
		} else 
		{
			include('index_maintenance.php');
		};
	echo '</section>';
	echo '<footer>';
			include_once('PiedDePage.php');
	echo '</footer>';
echo '</div>';
echo '</body>';
echo '</html>';
