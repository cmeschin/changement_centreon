<?php
if (session_id()=='')
{
session_start();
};
if ($_SESSION['ID_dem'] == 0)
{
	echo '<p>Vous devez saisir les informations générales et valider votre sélection sur les onglets précédents avant de passer au paramétrage!</p>';
	return false;
};
$ID_Demande = $_SESSION['ID_dem'];
$NbFieldset_Service = (isset($_POST["NbFieldset_Service"])) ? $_POST["NbFieldset_Service"]+1 : 1;
$_SESSION['PDF'] = false;
$_SESSION['Extraction'] = false; // sur un ajout on force systématiquement à NULL

echo '<fieldset id="Service' . $NbFieldset_Service . '" class="service">';
echo '<legend>Service n°' . $NbFieldset_Service . '</legend>';

echo '<!-- Nom service -->';
	echo '<label for="Nom_Service' . $NbFieldset_Service . '">Nom de la sonde :</label>';
	echo '<input type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Nom" onblur="verifChamp(this);verifNom_Service(this)" value="" size="40" maxlength="100" class="service' . $NbFieldset_Service . '"/>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nom_Service' . $NbFieldset_Service . '" />';

echo '<!-- Hote du service -->';
	echo '<label for="Hote_Service' . $NbFieldset_Service . '">Hôte de la sonde:</label>';
	echo '<select id="Hote_Service' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Hote" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Hote disponibles -->';
		echo '<option value="" selected >...</option> <!-- Valeur par défaut -->';
			include('connexion_sql_supervision.php'); 
			try {
				include_once('requete_liste_Service_Hote.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete liste service_hote: ' . $e->getMessage());
			};
			while ($res_Service_H = $req_Service_Hote->fetch())
			{
				echo '<option value="' . htmlspecialchars($res_Service_H['ID_Hote']) . '">' . htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) . '</option>';
			};
	echo '</select>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Hote_Service' . $NbFieldset_Service . '"/> <br />';

echo '<!-- Plage Horaire -->';
	echo '<label for="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle :</label>';
	echo '<select id="Service_Plage' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Plage_Horaire" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">  <!-- Liste Service_Plage -->';
		echo '<option value="" selected >...</option> <!-- Valeur par défaut -->';
			//include_once('connexion_sql_supervision.php'); 
			try {
				include_once('requete_liste_Service_Plage.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete liste service_Plage: ' . $e->getMessage());
			};
			while ($res_Service_P = $req_Service_Plage->fetch())
			{ 
				echo '<option value="' . htmlspecialchars($res_Service_P['Nom_Periode']) . '">' . htmlspecialchars($res_Service_P['Nom_Periode']) . '</option>'; 
			}; 
	echo '</select>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Plage' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)"/>';

echo '<!-- Controle_Actif -->';
	echo '<label for="Service_Actif' . $NbFieldset_Service . '">Contrôle :</label>';
	echo '<input Disabled="Disabled" type="text" id="Service_Actif' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Controle" onblur="verifChamp(this)" value="actif" size="5" class="service' . $NbFieldset_Service . '"/>';
	echo '<input Disabled="Disabled" style="visibility: hidden" type="text" id="Service_ID_Hote_Centreon' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_ID_Hote_Centreon" value="0" size="5" class="service' . $NbFieldset_Service . '"/><br/>';
	
echo '<!-- Modele service -->';
	echo '<label for="Service_Modele' . $NbFieldset_Service . '">Modèle :</label>';
	echo '<select id="Service_Modele' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Modele" onChange="afficher_argument(\'' . $NbFieldset_Service . '\')" onblur="verifChamp(this)" class="service' . $NbFieldset_Service . '">'; 
		echo '<option value="" selected >...</option> <!-- Valeur par défaut -->';
		//include_once('connexion_sql_supervision.php'); 
			try {
				include_once('requete_liste_Modele_Service.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete_liste_Modele_service: ' . $e->getMessage());
			};
		 
			while ($res_modele = $req_modele->fetch())
			{ 
				echo '<option value="' . htmlspecialchars($res_modele['ID_Modele_Service']) .'">' . htmlspecialchars($res_modele['Modele_Service']) .'</option>';
			};
	echo '</select>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Modele' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)"/>';

echo '<!-- Frequence -->';
	echo '<label for="Frequence_Service' . $NbFieldset_Service . '">Fréquence du contrôle :</label>';
	echo '<input type="text" id="Frequence_Service' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Frequence" onblur="verifChamp(this)" value="Par défaut" size="20" maxlength="20" class="service' . $NbFieldset_Service . '" title="Fréquence des principaux contrôles: Disque 30 minutes; Programmes, répertoires et sites web 5 minutes; Controles Vacation Bosmanager 15 minutes; controle Teleco 30 minutes; Controle certificats 1 fois par jour"/>';
	echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Frequence_Service' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)"/><br />';
	
	echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '">'; 
		$Description = "Sélectionner un modèle ci-dessus";
		$nbLibelle = 1;
		$T_Libelle[0] = "Libellé 1";
		$T_Argument_Mod[0] = "Argument 1";
		$T_Argument[0] = "";
		$Num_Argument = 1;
	
		include('gestion_affichage_arguments.php');
	echo '</fieldset><br />';

echo '<!-- Service Consigne -->';
	echo '<span id="Service_Consigne' . $NbFieldset_Service . '" class="service' . $NbFieldset_Service . '">Lien vers la consigne :<a id="Service_Consigne_lien' . $NbFieldset_Service . '" href="" target="_blank"></a></span>	<br />';
	
echo '<!-- Service Consigne Description -->';
	echo '<label for="Consigne_Service_Detail' . $NbFieldset_Service . '" onclick="alert(\'Décrivez ici les opérations à effectuer par les équipes EPI et/ou CDS si un évènement se produit sur l équipement (relancer un process, envoyer un mail, etc...).\nLes consignes doivent être claires et précises afin qu elles puissent être appliquées rapidement et sans ambiguïté par les équipes de support.\nLes adresses mails doivent être indiquées en toute lettre soit par ex: envoyer un mail à support_bmd@tessi.fr et pas simplement envoyer un mail support bmd.\nCette consigne sera ensuite retranscrite dans le wiki tessi-techno et un lien sera rattaché à l hôte; le lien apparaitra par la suite dans le champ ci-dessus.\')">Description consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<textarea id="Consigne_Service_Detail' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Description_Consigne" onblur="verifChamp(this)" rows="3" cols="50" class="service' . $NbFieldset_Service . '"></textarea>';
	echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Consigne_Service_Detail' . $NbFieldset_Service . '" ondblclick="deverouille_liste(this)"/><br />';
	
echo '<!-- Action à effectuer -->';
	echo '<fieldset id="Action_Service' . $NbFieldset_Service . '" class="service_action">';
		echo '<legend>Actions à effectuer</legend>';
		echo '<select id="Service_action' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Action" class="service' . $NbFieldset_Service . '">';
			echo '<option value="Creer">A créer</option>';
		echo '</select> <br />';
echo '<!-- Service Commentaire -->';
		echo '<label for="Service_Commentaire' . $NbFieldset_Service . '" onclick="alert(\'Indiquez ici toute information complémentaire utile au paramétrage. Dans cette zone vous pouvez également indiquer le nouveau nom du service s il doit être changé.\')">Commentaire  <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<textarea id="Service_Commentaire' . $NbFieldset_Service . '" name="Service_' . $NbFieldset_Service . '_Commentaire" rows="3" cols="50" class="service' . $NbFieldset_Service . '"></textarea> <br />';

	echo '</fieldset>';

	echo '<span id="bouton_Service' . $NbFieldset_Service . '" >';
		echo '<button id="Cloner_Service' . $NbFieldset_Service . '" onclick="clone_fieldset_service(this)">Dupliquer</button>';
		echo '<button id="Effacer_Service' . $NbFieldset_Service . '" onclick="efface_fieldset_service(this)" hidden="hidden">Effacer</button>';
		echo '<button id="Supprimer_Service' . $NbFieldset_Service . '" onclick="supprime_fieldset_service(this)">Retirer de la demande</button>';
	echo '</span>';
	if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Service.php');
	};
echo '</fieldset>';