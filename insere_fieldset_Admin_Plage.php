<?php
if (session_id()=='')
{
session_start();
};
if (isset($ID_Plage))
{
	try {
		$etat_dem = htmlspecialchars($res_liste_plage['Etat_Parametrage']);
		include('requete_liste_Etat_Demande.php');
	} catch (Exception $e) {
		http_response_code(500);
		die('Erreur requete liste Etat demande:' . $e->getMessage());
	};

	include('gestion_class_etat_dem.php');
	$fieldset_admin = True;
	echo '<fieldset id="Admin_bouton_Plage' . $NbFieldset_plage . '" class="Admin_bouton_Plage">';
	echo '<legend>Administration</legend>';
	echo '<label for="Etat_Plage' . $NbFieldset_plage . '">Etat:</label>';
	if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		echo '<select class="etat_dem_' . $etat_class . '" name="Etat_Plage' . $NbFieldset_plage . '" id="Etat_Plage' . $NbFieldset_plage . '" >';
// 		while ($res_etat = $req_etat->fetch())
// 		{ 
// 			if ( htmlspecialchars($res_etat['Etat_Dem']) != "Supprimer") // on ne peut pas supprimer un élément unitaire de la demande, donc il n'est pas ajouté à la liste.
// 			{
// 				if ($etat_dem == htmlspecialchars($res_etat['Etat_Dem']))
// 				{
// 					echo '<option Selected="Selected" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
// 				} else
// 				{
// 					echo '<option value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
// 				};
// 			};
// 		};
		include('insertion_liste_etat_dem.php');
		echo '</select>';
		echo '';
		echo '<button id="Enregistrer_Etat_Plage' . $NbFieldset_plage . '" onclick="enregistre_Etat_Demande(this,' . $ID_Plage . ')">Enregistrer</button>';
	} else 
	{
		echo '<input class="etat_dem_' . $etat_class . '" readonly type="text" id="Etat_Plage' . $NbFieldset_plage . '" name="Etat_Plage' . $NbFieldset_plage . '" value="' . $etat_dem . '"/>';
	};
	if ($etat_dem == "Annulé" && $res_liste_plage['motif_annulation'] != "")
	{
		echo '<p>Motif: ' . $res_liste_plage['motif_annulation'] . '</p>';
	};
	echo '</fieldset>';
	$fieldset_admin = False;
};
