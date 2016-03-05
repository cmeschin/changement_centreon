<!DOCTYPE html>
<?php
if (session_id()=='')
{
session_start();
};
$ID_Demande = (isset($_GET["id_dem"])) ? $_GET["id_dem"] : NULL; 	// si l'id_dem est transmis dans l'URL
$Recherche = (isset($_GET["recherche"])) ? $_GET["recherche"] : NULL; 	// si recherche est transmis dans l'URL

$ID_Demande = htmlspecialchars($ID_Demande); // on s'assure de la validité de la valeur transmise
$Recherche = htmlspecialchars($Recherche); // on s'assure de la validité de la valeur transmise

$_SESSION['Nouveau'] = false;	// Il ne s'agit pas d'une nouvelle demande
$_SESSION['Reprise'] = false;	// Il ne s'agit pas d'une reprise
$_SESSION['Extraction'] = false; // Il ne s'agit pas d'une extraction
$_SESSION['PDF'] = false;	// Il ne s'agit pas d'un extraction PDF
	
echo '<html>';
echo '<head>';
include('top.php');
include('head.php');
echo '</head>';
echo '<body>';
echo '<div id="principal">';
	echo '<header id="en-tete">';
	include('menu.php');
	echo '</header>';
	echo '<section>';
		/**
		 *  si Id_Demande transmis dans l'URL on charge simplement le contenu de la demande sinon on charge les deux onglets
		 */
		if ((($ID_Demande != NULL ) AND (is_numeric($ID_Demande))) OR ($Recherche != NULL))
		{
			$_SESSION['Recherche'] = true; // Il s'agit d'une recherche
			echo '<div id="tabs_DEC">';
				 echo '<ul>';
					echo '<li><a href="#tabs_DEC-1">Demande recherchée</a></li>';
				echo '</ul>';
			echo '<div id="tabs_DEC-1">';
				echo '<h2>Voici le détail de la demande recherchée</h2>';
				include('recherche_demande.php');
			echo '</div>';
			echo '</div>';
		} else 
		{
		if (($ID_Demande != NULL ) AND (is_numeric($ID_Demande) == false))
		{
			echo '<p>La référence tranmise n\'est pas un entier (' . $ID_Demande . '). Affichage de toutes les demandes en cours.</p>'; 
		}
			echo '<div id="tabs_DEC">';
				 echo '<ul>';
					echo '<li><a href="#tabs_DEC-1">Liste des demandes en cours</a></li>';
					echo '<li><a href="#tabs_DEC-2">Liste des demandes traitées ou annulées</a></li>';
				echo '</ul>';
				echo '<div id="tabs_DEC-1">';
					echo '<h2>Les brouillons et demandes à traiter</h2>';
					include_once('liste_demande_encours.php');
				echo '</div>';
				echo '<div id="tabs_DEC-2">';
					echo '<h2>Les demandes traitées ou annulées regroupées par mois</h2>';
					include_once('liste_demande_traitees.php');
				echo '</div>';
			echo '</div>';
		};
	echo '</section>';
	echo '<footer>';
	include('PiedDePage.php');
	echo '</footer>';
echo '</div>';
include('section_script_JS.php');
echo '</body>';
echo '</html>';
