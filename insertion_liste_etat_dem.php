<?php
if (session_id () == '') {
	session_start ();
};
include_once('connexion_sql_supervision.php');
include('requete_liste_Etat_Demande.php');
while ($res_etat = $req_etat->fetch())
{
	if ((htmlspecialchars($res_etat['Etat_Dem']) != "Supprimer") || (htmlspecialchars($res_etat['Etat_Dem']) == "Supprimer") && ($fieldset_admin == False)) // on ne peut pas supprimer un élément unitaire de la demande, donc il n'est pas ajouté à la liste.
	{
		//if (htmlspecialchars($res_dem['Etat_Demande']) == htmlspecialchars($res_etat['Etat_Dem']))
		if ($etat_dem == htmlspecialchars($res_etat['Etat_Dem']))
		{
			echo '<option Selected="Selected" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
		} elseif (htmlspecialchars($res_etat['statut']) == "inactif") // si le choix est inactif on le désactive dans la liste déroulante
		{
			echo '<option Disabled="Disabled" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
		} elseif (($etat_dem == "A Traiter") && (htmlspecialchars($res_etat['Etat_Dem']) == "En cours") && $fieldset_admin == False) // si la demande est A traiter et le statut a insérer est "En cours" on désactive
		{
			echo '<option Disabled="Disabled" value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
		}
		else // sinon on l'affiche simplement
		{
			echo '<option value="' . htmlspecialchars($res_etat['Etat_Dem']) . '">' . htmlspecialchars($res_etat['Etat_Dem']) . '</option> ';
		};
	};
};
