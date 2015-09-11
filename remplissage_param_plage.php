<?php
if (session_id()=='')
{
session_start();
};
// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];

include_once('connexion_sql_supervision.php');

try {
	include_once('requete_liste_periode_demande.php');
} catch (Exception $e) {
	die('Erreur requete_liste_periode_demande: '. $e->getMessage());
};
// Selection de toutes les plages de la demande
//$req_liste_plage = $bdd_supervision->prepare('SELECT Nom_Periode, if(Lundi="","-",Lundi), if(Mardi="","-",Mardi), if(Mercredi="","-",Mercredi), if(Jeudi="","-",Jeudi), if(Vendredi="","-",Vendredi), if(Samedi="","-",Samedi), if(Dimanche="","-",Dimanche), Type_Action, Etat_Parametrage FROM periode_temporelle WHERE Type_Action <> "OK" AND ID_Demande = :ID_Demande ORDER BY Nom_Periode');
//$req_liste_plage = $bdd_supervision->prepare('SELECT Nom_Periode, if(Lundi="","-",Lundi), if(Mardi="","-",Mardi), if(Mercredi="","-",Mercredi), if(Jeudi="","-",Jeudi), if(Vendredi="","-",Vendredi), if(Samedi="","-",Samedi), if(Dimanche="","-",Dimanche), Type_Action, Etat_Parametrage FROM periode_temporelle WHERE ID_Demande = :ID_Demande ORDER BY Type_Action, Nom_Periode');
//$req_liste_plage->execute(Array(
//	'ID_Demande' => htmlspecialchars($ID_Demande)
//)) or die(print_r($req_liste_plage->errorInfo()));

//$res_liste_plage = $req_liste_plage->fetchAll();

//$Nb_Plage = count($req_liste_plage);
//$tableau[$j]=$liste_hote;
//$chaine=implode(";",$liste_hote[$j]);
//$liste_plage = "";
$NbFieldset_plage = 1;
if ($req_liste_plage != NULL)
{
//	$NbFieldset_plage = 1;
	while ($res_liste_plage = $req_liste_plage->fetch())
//	foreach($res_service_Dem as $champ)
	{ 
	/*
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[0]) . '!'; // Nom_Periode
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[1]) . '!'; // Lundi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[2]) . '!'; // Mardi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[3]) . '!'; // Mercredi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[4]) . '!'; // Jeudi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[5]) . '!'; // Vendredi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[6]) . '!'; // Samedi
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[7]) . '|'; // Dimanche
		$liste_plage = $liste_plage . htmlspecialchars($res_liste_plage[8]) . '|'; // Type_Action
	*/
//		if ($res_liste_plage[8] != "OK")
//		{
		echo '<fieldset id="Plage' . $NbFieldset_plage . '" class="plage">';
		echo '<legend>Plage horaire n°' . $NbFieldset_plage . '</legend>';
			echo '<div id="model_param_plage">';
				echo '<!-- Nom_Période -->';
				echo '<label for="Nom_Plage' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez un nom pour la période. Choisissez un nom qui vous permettra de le retrouver dans la liste des périodes pour le paramétrage des services.\\nLe nom de la période pourra être modifié selon les besoins du paramétrage dans Centreon (doublon, ambiguïté, etc...)\')">Nom de la plage horaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				//echo '<input Disabled="Disabled" type="text" id="Nom_Plage' . $NbFieldset_plage . '" name="Nom_Plage' . $NbFieldset_plage . '" onblur="verifChamp(this)" value="' . $res_liste_plage[0] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '" title="Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h. Si la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')"/>';
				echo '<input Disabled="Disabled" type="text" id="Nom_Plage' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Nom" value="' . $res_liste_plage['nom_periode'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ver.png" class="verif" alt="correct" id="img_Nom_Plage' . $NbFieldset_plage . '"/> <br />';
				echo '';
				echo '<!-- Lundi -->';
				echo '<label for="Lundi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Lundi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Lundi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Lundi" onblur="verifChamp(this)" value="' . $res_liste_plage['lundi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Lundi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Mardi -->';
				echo '<label for="Mardi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Mardi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Mardi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Mardi" onblur="verifChamp(this)" value="' . $res_liste_plage['mardi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Mardi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Mercredi -->';
				echo '<label for="Mercredi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Mercredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Mercredi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Mercredi" onblur="verifChamp(this)" value="' . $res_liste_plage['mercredi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Mercredi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Jeudi -->';
				echo '<label for="Jeudi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Jeudi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Jeudi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Jeudi" onblur="verifChamp(this)" value="' . $res_liste_plage['jeudi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Jeudi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Vendredi -->';
				echo '<label for="Vendredi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Vendredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Vendredi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Vendredi" onblur="verifChamp(this)" value="' . $res_liste_plage['vendredi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Vendredi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Samedi -->';
				echo '<label for="Samedi' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Samedi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Samedi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Samedi" onblur="verifChamp(this)" value="' . $res_liste_plage['samedi'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Samedi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Dimanche -->';
				echo '<label for="Dimanche' . $NbFieldset_plage . '" size="50" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\')">Dimanche <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<input Disabled="Disabled" type="text" id="Dimanche' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Dimanche" onblur="verifChamp(this)" value="' . $res_liste_plage['dimanche'] . '" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
				echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Dimanche' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
				echo '';
				echo '<!-- Commentaire -->';
				echo '<label for="Commentaire_Plage' . $NbFieldset_plage . '" onclick="alert(\'Saisissez ici tout complément d\\\'information qui serait utile...\')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
				echo '<textarea id="Commentaire_Plage' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Commentaire" rows="3" cols="50" class="plage' . $NbFieldset_plage . '">' . $res_liste_plage['commentaire'] . '</textarea> <br />';
				echo '';
				echo '<fieldset id="Action_Plage' . $NbFieldset_plage . '" class="plage_action">';
				echo '<legend>Actions à effectuer</legend>';
					echo '<select name="Plage_action' . $NbFieldset_plage . '" id="Plage_action' . $NbFieldset_plage . '" class="plage' . $NbFieldset_plage . '">';
						//echo '<option Selected="Selected" value="OK">OK</option> ';
						echo '<option Selected="Selected" value="Modifier">A Modifier</option> ';
					echo '</select>';
				echo '</fieldset>';
				echo '<span id="bouton_Plage' . $NbFieldset_plage . '">';
					echo '<button id="Cloner_Plage' . $NbFieldset_plage . '" onclick="clone_fieldset_Plage(this)" >Dupliquer</button>';
					echo '<button id="Effacer_Plage' . $NbFieldset_plage . '" onclick="efface_fieldset_Plage(this)" hidden>Effacer</button>';
					echo '<button id="Supprimer_Plage' . $NbFieldset_plage . '" onclick="supprime_fieldset_Plage(this)" title="N\'enregistre pas les modifications apportées. La plage horaire reste disponible avec les valeurs actuelles pour le paramétrage des services." >Retirer de la demande</button>';
				echo '</span> <br />';
				echo '';
				if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
				{
					include('insere_fieldset_Admin_Plage.php');
				};
			echo '</div>';
		echo '</fieldset>';
	//	};
	//	echo '</div>';
		$NbFieldset_plage++; // incrémentation du fieldset
	};
} else
{
	echo '<fieldset id="Aucune_Plage" class="plage">';
	echo '<legend>Aucune plage horaire</legend>';
	echo '<p>Aucune plage horaire n\'est définie pour cette prestation. Vous devez en ajouter au moins une avant de passer au paramétrage des services.</p>';
	echo '</fieldset>';
};
//$liste_plage = rtrim($liste_plage,'|');

//echo $liste_plage;
$Statut_Plage=true;
