<?php
if (session_id () == '') {
	session_start ();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).

$etat_dem = (isset($_POST["Etat_Param"])) ? $_POST["Etat_Param"] : NULL;

if ($etat_dem)
{
	include('insertion_liste_etat_dem.php');
} else 
{
	echo "ERREUR: Etat_Param=[" . $etat_dem . "].";
};
