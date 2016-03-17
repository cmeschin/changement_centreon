<?php
if (session_id()=='')
{
session_start();
};
if (isset($ID_Hote))
{
	try {
		$etat_dem = $res_liste_hote['Etat_Parametrage'];
		include('requete_liste_Etat_Demande.php');
	} catch (Exception $e) {
// inutile puisque le select n'est pas encore ouvert
//		echo '</select>';
		http_response_code(500);
		die('Erreur selection etat demande: ' . $e->getMessage());
	};
	$etat=htmlspecialchars($res_liste_hote['Etat_Parametrage']);
	include('gestion_class_etat_dem.php');
	
	echo '<fieldset id="Admin_bouton_Hote' . $NbFieldset . '" class="Admin_bouton_Hote">';
	echo '<legend>Administration</legend>';
	echo '<label for="Etat_Hote' . $NbFieldset . '">Etat:</label>';
	if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		echo '<select class="etat_dem_' . $etat_class . '" name="Etat_Hote' . $NbFieldset . '" id="Etat_Hote' . $NbFieldset . '" >';
		while ($res_etat = $req_etat->fetch())
		{ 
			if ( htmlspecialchars($res_etat['Etat_Dem']) != "Supprimer") // on ne peut pas supprimer un élément unitaire de la demande, donc il n'est pas ajouté à la liste.
			{
				if (htmlspecialchars($res_liste_hote['Etat_Parametrage']) == htmlspecialchars($res_etat['Etat_Dem']))
				{
					echo '<option Selected="Selected" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
				} else
				{
					echo '<option value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
				};
			};
		};
		echo '</select>';
		echo '';
		echo '<button id="Enregistrer_Etat_Hote' . $NbFieldset . '" onclick="enregistre_Etat_Demande(this,' . $ID_Hote . ')">Enregistrer</button>';
	} else 
	{
		echo '<input class="etat_dem_' . $etat_class . '" readonly name="Etat_Hote' . $NbFieldset . '" id="Etat_Hote' . $NbFieldset . '" value="' . $etat . '"/>';
	};
	
	if ($res_liste_hote['Etat_Parametrage'] == "Annulé" && $res_liste_hote['motif_annulation'] != "")
	{
		echo '<p>Motif: ' . $res_liste_hote['motif_annulation'] . '</p>';
	};
	echo '</fieldset>';
};