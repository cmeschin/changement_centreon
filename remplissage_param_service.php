<?php
if (session_id()=='')
{
session_start();
};
// récupération de la ref demande
$ID_Demande= $_SESSION['ID_dem'];
//$ID_Demande= 7;
//include('log.php'); // chargement de la fonction de log

include_once('connexion_sql_supervision.php');

// Selection de tous les services de la demande
try {
	include('requete_Remplissage_Service.php');
} catch (Exception $e) {
	die('Erreur requete_Remplissage_Service: ' . $e->getMessage());
};


//$Nb_Hote = count($req_liste_hote);
//$tableau[$j]=$liste_hote;
//$chaine=implode(";",$liste_hote[$j]);
$liste_service = "";
//$Num_Service_Modele = "";
$NbFieldset_Service = 1;
while ($res_liste_service = $req_liste_service->fetch())
{ 
/*
// Detail de la requête
// Nom_Service			0
// Nom_Hote				1
// IP_Hote				2
// ID_Localisation		3
// Nom_Periode			4
// Frequence			5
// Consigne				6
// Controle_Actif		7
// MS_Modele_Service	8
// MS_Libelles			9
// Parametres			10
// Detail_Consigne		11
// Type_Action			12
// Etat_Parametrage		13
// ID_Service			14
// Commentaire			15
// MS_Description		16
// MS_Arguments			17
// MS_Macro				18
// MS_EST_MACRO			19
// ID_Hote				20
// ID_Hote_Centreon		21
// ID_Service_Centreon	22
*/
	$LongueurArg=  strlen(htmlspecialchars($res_liste_service['Nom_Service'])) + 5;
	echo '<fieldset id="Service' . $NbFieldset_Service . '" class="service">';
	echo '<legend>Service n°' . $NbFieldset_Service . '</legend>';
	echo '';
		echo '<!-- Nom service -->';
		echo '<label for="Nom_Service' . $NbFieldset_Service . '">Nom de la sonde :</label>';
		//echo '<input Readonly type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Nom_Service' . $NbFieldset_Service . '" onblur="verifChamp(this)" value="' . htmlspecialchars($res_liste_service['Nom_Service']) . '" size="40" maxlength="100" class="service' . $NbFieldset_Service . '"/>';
		echo '<input Readonly type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Nom" value="' . htmlspecialchars($res_liste_service['Nom_Service']) . '" size="'. $LongueurArg .'" maxlength="100" class="service' . $NbFieldset_Service . '"/>';
		echo '<img src="images/img_ver.png" class="verif" alt="correct" id="img_Nom_Service' . $NbFieldset_Service . '" />';
		echo '';
		echo '<!-- Hote du service -->';
		echo '<label for="Hote_Service' . $NbFieldset_Service . '">Hôte de la sonde:</label>';
		echo '<select Disabled="Disabled" name="Service_' . $NbFieldset_Service . '_Hote" id="Hote_Service' . $NbFieldset_Service . '" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Hote disponibles -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			try {
				include('requete_liste_Service_Hote.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete_liste_Service_Hote: ' . $e->getMessage());
			};
			
			while ($res_Service_H = $req_Service_Hote->fetch())
			{
				if ($res_liste_service['Nom_Hote'] == $res_Service_H['Nom_Hote']){
//					echo '<option Selected="Selected" value="' . htmlspecialchars($res_Service_H['Nom_Hote']) . '">' . htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) . '</option> ';
					echo '<option Selected="Selected" value="' . htmlspecialchars($res_Service_H['ID_Hote']) . '">' . htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) . '</option> ';
				} else {
//					echo '<option value="' . htmlspecialchars($res_Service_H['Nom_Hote']) . '">' . htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) . '</option> ';
					echo '<option value="' . htmlspecialchars($res_Service_H['ID_Hote']) . '">' . htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) . '</option> ';
				};
			};
		echo '</select>';
//		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Hote_Service' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/> <br />';
		echo '<img src="images/img_ver.png" class="verif" alt="correct" id="img_Hote_Service' . $NbFieldset_Service . '"/> <br />';
		echo '';
		echo '<!-- Plage Horaire -->';
		echo '<label for="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle :</label>';
		echo '<select Disabled="Disabled" name="Service_' . $NbFieldset_Service . '_Plage" id="Service_Plage' . $NbFieldset_Service . '" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Service_Plage -->';
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
			try {
				include('requete_liste_Service_Plage.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete_liste_Service_Plage: ' . $e->getMessage());
			}; 
			while ($res_Service_P = $req_Service_Plage->fetch())
			{
				if ($res_liste_service['Nom_Periode'] == $res_Service_P['Nom_Periode']){
					echo '<option Selected="Selected" value="' . htmlspecialchars($res_Service_P['Nom_Periode']) . '">' . htmlspecialchars($res_Service_P['Nom_Periode']) . '</option> ';
				} else {
					echo '<option value="' . htmlspecialchars($res_Service_P['Nom_Periode']) . '">' . htmlspecialchars($res_Service_P['Nom_Periode']) . '</option> ';
				};
			};
		echo '</select>';
		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Service_Plage' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		echo '';
		echo '<!-- Controle_Actif -->';
		echo '<label for="Service_Actif' . $NbFieldset_Service . '">Contrôle :</label>';
		echo '<input Disabled="Disabled" type="text" id="Service_Actif' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Actif" onblur="verifChamp(this)" value="' . htmlspecialchars($res_liste_service['Controle_Actif']) . '" size="5" class="service' . $NbFieldset_Service . '"/>';
		echo '<input Disabled="Disabled" style="visibility: hidden" type="text" id="Service_ID_Hote_Centreon' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_ID_Hote_Centreon" value="' . htmlspecialchars($res_liste_service['ID_Hote_Centreon']) . '" size="5" class="service' . $NbFieldset_Service . '"/> <br />';
		echo '';
		echo '<!-- Modele service -->';
		echo '<label for="Service_Modele' . $NbFieldset_Service . '">Modèle :</label>';
		//$Num_Service_Modele = "Service_Modele" . $NbFieldset_Service;
//		echo '<select Disabled="Disabled" name="Service_' . $NbFieldset_Service . '_Modele" id="Service_Modele' . $NbFieldset_Service . '" onChange="sauve_argument(' . $NbFieldset_Service . ',' . $res_liste_service['MS_Modele_Service'] . ');afficher_argument(' . $NbFieldset_Service . ')" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Type_Service -->';
		echo '<select Disabled="Disabled" name="Service_' . $NbFieldset_Service . '_Modele" id="Service_Modele' . $NbFieldset_Service . '" onChange="sauve_argument(' . $NbFieldset_Service . ',\'' . $res_liste_service['MS_Modele_Service'] . '\');afficher_argument(' . $NbFieldset_Service . ')" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Type_Service -->';
		try {
				include('requete_liste_Modele_Service.php'); 
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete_liste_Modele_Service: ' . $e->getMessage());
			}; 
			echo '<option value="" >...</option> <!-- Valeur par défaut -->';
/*
			while ($res_modele = $req_modele->fetch())
			{
				if ($res_liste_service['MS_Modele_Service'] == $res_modele['Modele_Service']){
					echo '<option Selected="Selected" value="' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'">' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'</option>';
				} else {
					echo '<option value="' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'">' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'</option>';
				};
			};
*/
		if ($res_liste_service['MS_Modele_Service'] == "")
		{
			$Trouve_modele = true; // on force à true pour le champ masqué
			while ($res_modele = $req_modele->fetch())
			{
				echo '<option value="' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'">' . htmlspecialchars($res_liste_service['MS_Modele_Service']) .'</option>';
			};
		} else
		{
			$res_modele = $req_modele->fetchAll();
			$Trouve_modele = false;
			foreach($res_modele as $champ)
			{
				if ($res_liste_service['MS_Modele_Service'] == $champ['Modele_Service'])
				{
					$Trouve_modele = true;
				};
			};
			if ($Trouve_modele == true)
			{
				foreach($res_modele as $champ)
				{
					if ($res_liste_service['MS_Modele_Service'] == $champ['Modele_Service'])
					{
						echo '<option Selected="Selected" value="' . htmlspecialchars($champ['ID_Modele_Service']) . '">' . htmlspecialchars($champ['Modele_Service']) . '</option>';
					} else
					{
						echo '<option value="' . htmlspecialchars($champ['ID_Modele_Service']) . '">' . htmlspecialchars($champ['Modele_Service']) . '</option>';
					};
				};
			} else
			{
				foreach($res_Loc as $champ)
				{
					echo '<option value="' . htmlspecialchars($champ['ID_Modele_Service']) . '">' . htmlspecialchars($champ['Modele_Service']) . '</option>';
				};
			};
		};
//		addlog("Trouve_modele=". $Trouve_modele);

		echo '</select>';
		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Service_Modele' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/>';
		echo '<!-- Frequence -->';
		echo '<label for="Frequence_Service' . $NbFieldset_Service . '">Fréquence du contrôle :</label>';
		echo '<input Disabled="Disabled" type="text" id="Frequence_Service' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Frequence" onblur="verifChamp(this)" value="' . htmlspecialchars($res_liste_service['Frequence']) . '" size="20" maxlength="20" class="service' . $NbFieldset_Service . '" title="Fréquence des principaux contrôles: Disque 30 minutes; Programmes, répertoires et sites web 5 minutes; Controles Vacation Bosmanager 15 minutes; controle Teleco 30 minutes; Controle certificats 1 fois par jour"/>';
		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Frequence_Service' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)" title="double-clic pour déverrouiller le champ"/><br/>';
		echo '';
		echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '">';
			//gestion des arguments
			include('gestion_arguments.php');
		echo '</fieldset> ';
		echo ' <br />';
		echo '<!-- Service Consigne -->';
		echo '<label for="Service_Consigne' . $NbFieldset_Service . '">Lien vers la consigne :</label>';
		echo '<input type="text" id="Service_Consigne' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Lien_Consigne" value="' . htmlspecialchars($res_liste_service['Consigne']) . '" size="70" maxlength="255" class="service' . $NbFieldset_Service . '"/> <br />';
		echo '';
		echo '<!-- Service Consigne Description-->';
		echo '<label for="Consigne_Service_Detail' . $NbFieldset_Service . '">Description consigne :</label>';
		echo '<textarea id="Consigne_Service_Detail' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Description_Consigne" rows="3" cols="50" class="service' . $NbFieldset_Service . '">' . htmlspecialchars($res_liste_service['Detail_Consigne']) . '</textarea> <br />';
		echo '';
		echo '<!-- Action à effectuer -->';
		echo '<fieldset id="Action_Service' . $NbFieldset_Service . '" class="service_action">';
		echo '<legend>Actions à effectuer</legend>';
			echo '<select name="Service_' . $NbFieldset_Service . '_Action" id="Service_action' . $NbFieldset_Service . '" class="service' . $NbFieldset_Service . '">';
				//addlog("type_action=". htmlspecialchars($res_liste_service['Nom_Service']));
				//addlog("type_action=". htmlspecialchars($res_liste_service['Type_Action']));
				if (htmlspecialchars($res_liste_service['Type_Action']) == "Modifier")
				{
					echo '<option Selected="Selected" value="Modifier">A Modifier</option>';
				} else if (htmlspecialchars($res_liste_service['Type_Action']) == "Creer")
				{
					echo '<option Selected="Selected" value="Creer">A Créer</option>';
				};
				if (htmlspecialchars($res_liste_service['Controle_Actif']) == "actif")
				{
					if (htmlspecialchars($res_liste_service['Type_Action']) == "Desactiver")
					{
						echo '<option Selected="Selected" value="Desactiver">A Désactiver</option>';
					} else
					{
						echo '<option value="Desactiver">A Désactiver</option>';
					};
				} else 
				{
					if (htmlspecialchars($res_liste_service['Type_Action']) == "Activer")
					{
						echo '<option Selected="Selected" value="Activer">A Activer</option>';
					} else
					{
						echo '<option value="Activer">A Activer</option>';
					};
				};
				if (htmlspecialchars($res_liste_service['Type_Action']) == "Supprimer")
				{
					echo '<option Selected="Selected" value="Supprimer">A Supprimer</option>';
				} else
				{
					echo '<option value="Supprimer">A Supprimer</option>';
				};
			echo '</select>';
			echo '<br />';
			echo '<!-- Service Commentaire -->';
			echo '<label for="Service_Commentaire' . $NbFieldset_Service . '">Commentaire :</label>';
			echo '<textarea id="Service_Commentaire' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Commentaire" rows="3" cols="50" class="service' . $NbFieldset_Service . '" title="Indiquez ici toute information complémentaire utile au paramétrage; Dans cette zone vous pouvez également indiquer le nouveau nom du service s\'il doit être changé car peu explicite.">' . htmlspecialchars($res_liste_service['Commentaire']) . '</textarea> <br />';

		echo '</fieldset>';
		echo '';
		echo '<span id="bouton_Service' . $NbFieldset_Service . '" >';
		echo '<button id="Cloner_Service' . $NbFieldset_Service . '" onclick="clone_fieldset_service(this)">Dupliquer</button>';
		echo '<button id="Effacer_Service' . $NbFieldset_Service . '" onclick="efface_fieldset_service(this)" hidden>Effacer</button>';
		echo '<button id="Supprimer_Service' . $NbFieldset_Service . '" onclick="supprime_fieldset_service(this)">Retirer de la demande</button>';
		echo '</span>';
		echo '';
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
		{
			include('insere_fieldset_Admin_Service.php');
		};
		echo '';
	echo '</fieldset>';
	$NbFieldset_Service ++;
};
$Statut_Service=true;
