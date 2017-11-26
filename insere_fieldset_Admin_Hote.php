<?php
if (session_id()=='')
{
session_start();
};
if (isset($ID_Hote))
{
	try {
		$etat_dem = htmlspecialchars($res_liste_hote['Etat_Parametrage']);
		include('requete_liste_Etat_Demande.php');
	} catch (Exception $e) {
		http_response_code(500);
		die('Erreur selection etat demande: ' . $e->getMessage());
	};

	include('gestion_class_etat_dem.php');
	$fieldset_admin = True;
	echo '<fieldset id="Admin_bouton_Hote' . $NbFieldset . '" class="Admin_bouton_Hote">';
	echo '<legend>Administration</legend>';
	echo '<label for="Etat_Hote' . $NbFieldset . '">Etat:</label>';
	if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		echo '<select class="etat_dem_' . $etat_class . '" name="Etat_Hote' . $NbFieldset . '" id="Etat_Hote' . $NbFieldset . '" >';
		include('insertion_liste_etat_dem.php');
		echo '</select>';
		echo '';
		echo '<button id="Enregistrer_Etat_Hote' . $NbFieldset . '" onclick="enregistre_Etat_Demande(this,' . $ID_Hote . ')">Enregistrer</button>';
	} else 
	{
		echo '<input class="etat_dem_' . $etat_class . '" readonly name="Etat_Hote' . $NbFieldset . '" id="Etat_Hote' . $NbFieldset . '" value="' . $etat_dem . '"/>';
	};
	
	if ($etat_dem == "Annulé" && $res_liste_hote['motif_annulation'] != "")
	{
		echo '<p>Motif: ' . $res_liste_hote['motif_annulation'] . '</p>';
	};
	echo '</fieldset>';
	$fieldset_admin = False;
};