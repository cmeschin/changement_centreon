<?php
if (session_id()=='')
{
session_start();
};
if (isset($ID_Service))
{
	try {
		$etat_dem = $res_liste_service['Etat_Parametrage'];
		include('requete_liste_Etat_Demande.php');
	} catch (Exception $e) {
		echo '</select>';
		http_response_code(500);
		die('Erreur requete liste etat demande:' . $e->getMessage());
	};
	
	$etat=htmlspecialchars($res_liste_service['Etat_Parametrage']);
	include('gestion_class_etat_dem.php');
	
	echo '<fieldset id="Admin_bouton_Service' . $NbFieldset_Service . '" class="Admin_bouton_Service">';
	echo '<legend>Administration</legend>';
	echo '<label for="Etat_Service' . $NbFieldset_Service . '">Etat:</label>';
	echo '<select class="etat_dem_' . $etat_class . '" name="Etat_Service' . $NbFieldset_Service . '" id="Etat_Service' . $NbFieldset_Service . '">';
	while ($res_etat = $req_etat->fetch())
	{ 
		if ( htmlspecialchars($res_etat['Etat_Dem']) != "Supprimer") // on ne peut pas supprimer un élément unitaire de la demande, donc il n'est pas ajouté à la liste.
		{
			if (htmlspecialchars($res_liste_service['Etat_Parametrage']) == htmlspecialchars($res_etat['Etat_Dem']))
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
	echo '<button id="Enregistrer_Etat_Service' . $NbFieldset_Service . '" onclick="enregistre_Etat_Demande(this,' . $ID_Service . ')">Enregistrer</button>';
	if ($res_liste_service['Etat_Parametrage'] == "Annulé" && $res_liste_service['motif_annulation'] != "")
	{
		echo '<p>Motif: ' . $res_liste_service['motif_annulation'] . '</p>';
	};
	echo '</fieldset>';
};