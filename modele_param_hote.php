<?php
if (session_id () == '') {
	session_start ();
}
;
if ($_SESSION ['ID_dem'] == 0) {
	echo '<p>Vous devez saisir les informations générales et valider votre sélection sur les onglets précédents avant de passer au paramétrage!</p>';
	return False;
}
;
$NbFieldset = (isset ( $_POST ["NbFieldset"] )) ? $_POST ["NbFieldset"] + 1 : 1;
$_SESSION['PDF'] = false;
$_SESSION['Extraction'] = false;

echo '<fieldset id="Hote' . $NbFieldset . '" class="hote">';
	echo '<legend>Hôte n°' . $NbFieldset . '</legend>';

	echo '<div id="model_param_hote">';
		echo '<!-- Hote -->';
		echo '<label for="Nom_Hote' . $NbFieldset . '" class="hote_Nom_IP" onclick="alert(\'Saisir le nom de l\'hôte tel que définit dans les propriétés systèmes.\nCaractères alphanumériques, tirets haut et bas uniquement.\')">Nom de l\'hôte <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Nom_Hote' . $NbFieldset . '"	name="Hote_' . $NbFieldset . '_Nom" onblur="verifChamp(this);majuscule(this);verifChampNonIP(this);verifNom_Hote(this)" value="" size="20" maxlength="20"	class="hote' . $NbFieldset . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nom_Hote' . $NbFieldset . '" /> <br />';

		echo '<!-- Adresse IP -->';
		echo '<label for="IP_Hote' . $NbFieldset . '" class="hote_Nom_IP">Adresse	IP :</label>';
		echo '<input type="text" id="IP_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_IP" onblur="verifChampIP(this)"	value="" class="hote' . $NbFieldset . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_IP_Hote' . $NbFieldset . '" />';
	echo '</div>';

	echo '<!-- Description -->';
	echo '<div id="model_param_hote">';
		echo '<label for="Hote_Description' . $NbFieldset . '" class="hote_Nom_IP" onclick="alert(\'Saisir ici une description succinte de l\'hôte. Pour les routeurs, switch, firewall, etc... merci d\'indiquer le modèle (SRX2200, HP Procurve, 24/48 ports, etc...).\')">Description <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br />';
		echo '<textarea id="Hote_Description' . $NbFieldset . '"	name="Hote_' . $NbFieldset . '_Description" rows="2" cols="40" onblur="verifChamp(this)" class="hote' . $NbFieldset . '"></textarea>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect"	id="img_Hote_Description' . $NbFieldset . '" />';
	echo '</div>';
	echo '<br />';

	echo '<!-- Localisation -->';
	echo '<label for="Localisation' . $NbFieldset . '" onclick="alert(\'Indiquez la localisation géographique de l\'hôte, si elle n\'apparait pas dans la liste, sélectionnez Autre et indiquez le nouveau site.\')">Localisation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<select	id="Localisation' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Localisation" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">';
		echo '<!-- Liste Localisation -->';
		echo '<option value="" selected>...</option>';
		echo '<!-- Valeur par défaut -->';
		echo '<option value="Autre">Autre</option>';
		echo '<!-- Valeur à sélectionner pour en créer un -->';

		include_once ('connexion_sql_supervision.php');
		try {
			include_once ('requete_liste_Hote_Site.php');
		} catch (Exception $e) {
			echo '</select>';
			http_response_code(500);
			die ('Erreur requete_liste_hote_Site' . $e->getMessage());
		};
		while ( $res_Loc = $req_Localisation->fetch () ) {
			include('option_localisation.php');
		};
	echo '</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Localisation' . $NbFieldset . '" />';
	echo '<span	id="Localisation' . $NbFieldset . '_new" style="visibility: hidden;">';
	echo '<input onblur="verifChamp(this)"	type="text" name="Localisation' . $NbFieldset . '_new" id="Localisation' . $NbFieldset . '_new" value="Vide" placeholder="par ex: Nanterre" size="20" class="hote' . $NbFieldset . '"	title="Saisir le nouveau site... Le nom final pourra être modifié afin de correspondre à la règle de nommage." />';
	echo '<img src="images/img_ok.png" class="verif" alt="correct"	id="img_Localisation' . $NbFieldset . '_new" />';
	echo '</span> <br />';

	echo '<!-- Type Hote -->';
	echo '<label for="Type_Hote' . $NbFieldset . '" onclick="alert(\'Sélectionnez le type d\'hôte dans la liste.\nCette information fait partie de la règle de nommage des hôtes dans Centreon.\')">Type <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<select id="Type_Hote' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Type" onChange="afficher_autre(this)" onblur="verifChamp(this)"	class="hote' . $NbFieldset . '">';
	echo '<!-- Liste Type_Hote -->';
	echo '<option value="" selected>...</option>';
	echo '<!-- Valeur par défaut -->';
	echo '<option value="Autre">Autre</option>';
	echo '<!-- Valeur à sélectionner pour en créer un -->';
		// include_once('connexion_sql_supervision.php');
		try {
			include_once ('requete_liste_Hote_Type.php');
		} catch (Exception $e) {
			echo '</select>';
			http_response_code(500);
			die ('Erreur requete_liste_hote_Type' . $e->getMessage());
		};
		while ( $res_type = $req_type->fetch () ) {
			include('option_type.php');
		};
	echo '</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_Hote' . $NbFieldset . '" />'; 
		echo '<span id="Type_Hote' . $NbFieldset . '_new" style="visibility: hidden;"> <input onblur="verifChamp(this)" type="text" name="Type_Hote' . $NbFieldset . '_new"	id="Type_Hote' . $NbFieldset . '_new" value="Vide"	placeholder="saisir le nouveau type d\'hôte..." size="30" class="hote' . $NbFieldset . '" title="Saisissez le nouveau type d\'hôte... Le nom final pourra être modifié afin de correspondre à la règle de nommage." />';
		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_Hote' . $NbFieldset . '_new" />';
	echo '</span> <br />';

	echo '<!-- OS -->';
	echo '<label for="Type_OS' . $NbFieldset . '" onclick="alert(\'Sélectionnez le système d\'exploitation installé sur l\'hôte. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]\')">Système d\'exploitation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<select id="Type_OS' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_OS" onChange="afficher_autre(this)" onblur="verifChamp(this)" class="hote' . $NbFieldset . '">';	

	echo '<!-- Liste Type_OS -->';
		echo '<option value="" selected>...</option>';
		echo '<!-- Valeur par défaut -->';
		echo '<option value="NC">Non Concerné</option>';
		echo '<!-- Valeur si Non Concerné -->';
		echo '<option value="Autre">Autre</option>';
		echo '<!-- Valeur à sélectionner pour en créer un -->';
		// include_once('connexion_sql_supervision.php');
		try {
			include_once ('requete_liste_Hote_OS.php');
		} catch (Exception $e) {
			echo '</select>';
			die ('Erreur requete_liste_hote_OS' . $e->getMessage());
		};
		while ( $res_OS = $req_OS->fetch () ) {
			echo '<option value="' . htmlspecialchars($res_OS['Type_OS']) .'">' . htmlspecialchars($res_OS['Type_OS']) .'</option>'; 
		};
	echo '</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_OS' . $NbFieldset . '" />';
		echo '<span	id="Type_OS' . $NbFieldset . '_new" style="visibility: hidden;">';
		echo '<input onblur="verifChamp(this)" type="text"	name="Type_OS' . $NbFieldset . '_new" id="Type_OS' . $NbFieldset . '_new" value="Vide" placeholder="par ex: Ubuntu 14.04 ou Windows 9" size="30" class="hote' . $NbFieldset . '" title="Indiquez le système d\'exploitation installé sur l\'hôte." />';
		echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_OS' . $NbFieldset . '_new" />';
	echo '</span> <br />';

	echo '<!-- Architecture -->';
	echo '<label for="Architecture' . $NbFieldset . '" onclick="alert(\'Indiquez s\'il s\'agit d\'un OS 32 ou 64 bits. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]\')">Architecture <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<select id="Architecture' . $NbFieldset . '"	name="Hote_' . $NbFieldset . '_Architecture" onChange="afficher_autre(this)" onblur="verifChamp(this)"	class="hote' . $NbFieldset . '">';
		echo '<!-- Liste Architecture -->';
		echo '<option value="" selected>...</option>';
		echo '<!-- Valeur par défaut -->';
		echo '<option value="NC">Non Concerné</option>';
		echo '<!-- Valeur si Non Concerné -->';
		echo '<option value="32_bits">32_bits</option>';
		echo '<option value="64_bits">64_bits</option>';
	echo '</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Architecture' . $NbFieldset . '" />';

	echo '<!-- Langue -->';
	echo '<label for="Langue' . $NbFieldset . '" onclick="alert(\'Sélectionnez la langue du système d\'exploitation installé sur l\'hôte. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]\')">Langue <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<select name="Langue' . $NbFieldset . '"	id="Langue' . $NbFieldset . '" onChange="afficher_autre(this)"	onblur="verifChamp(this)" class="hote' . $NbFieldset . '">';
		echo '<!-- Liste Langue -->';
		echo '<option value="" selected>...</option>';
		echo '<!-- Valeur par défaut -->';
		echo '<option value="NC">Non Concerné</option>';
		echo '<!-- Valeur si Non Concerné -->';
		echo '<option value="Francais">Français</option>';
		echo '<option value="Anglais">Anglais</option>';
	echo '</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Langue' . $NbFieldset . '" />';

	echo '<!-- Fonction -->';
	echo '<label for="Fonction' . $NbFieldset . '" onclick="alert(\'Indiquez la ou les fonctions principales de l\'hôte. Cette information permettra de catégoriser l\'équipement.\nLes fonctions principales sont les suivantes: BMChèque, BMDocument, Marcel, Tri, Videocodage, ICR, CFT, IBML, Fax, Parefeu, etc...\')">Fonction(s) <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<input type="text" id="Fonction' . $NbFieldset . '" name="Fonction' . $NbFieldset . '" value="" size="30" placeholder="par ex: Fax, Routeur, BMChèque, BosVideo, Marcel, etc..." maxlength="50" class="hote' . $NbFieldset . '"/>';

	echo '<br />';
	echo '<!-- Consigne -->';
	echo '<span id="Consigne_Hote' . $NbFieldset . '" class="hote' . $NbFieldset . '">Lien vers la consigne :<a href="" target="_blank"></a></span>';
	echo '<br />';

	echo '<!-- Detail consigne -->';
	echo '<label for="Consigne_Hote_Detail' . $NbFieldset . '" onclick="alert(\'Décrivez ici les opérations à effectuer par les équipes EPI et/ou CDS si un évènement se produit sur l\'équipement (relancer un process, envoyer un mail, etc...). Les consignes doivent être claires et précises afin qu\'elles puissent être appliquées rapidement et sans ambiguïté par les équipes de support. Les adresses mails doivent être indiquées en toute lettre soit par ex: envoyer un mail à support_bmd@tessi.fr et pas simplement envoyer un mail support bmd. Cette consigne sera ensuite retranscrite dans le wiki tessi-techno et un lien sera rattaché à l\'hôte.\')">Description consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
	echo '<textarea id="Consigne_Hote_Detail' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Description_Consigne" onblur="verifChamp(this)" rows="3" cols="50" class="hote' . $NbFieldset . '"></textarea>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Consigne_Hote_Detail' . $NbFieldset . '" ondblclick="deverouille_liste(this)"/>';
	echo '<!-- <br /> -->';

	echo '<!-- Controle_actif -->';
	echo '<label for="Controle_Actif_Hote' . $NbFieldset . '" sstyle="visibility: hidden">Controle :</label> <input type="text"	id="Controle_Actif_Hote' . $NbFieldset . '"	name="Controle_Actif' . $NbFieldset . '" readonly value="actif"	size="5" sstyle="visibility: hidden" class="hote' . $NbFieldset . '" /> <br />';

	echo '<!-- Action à  effectuer -->';
	echo '<fieldset id="Action_Hote' . $NbFieldset . '" class="hote_action">';
		echo '<legend onclick="alert(\'Sélectionnez l\'action à réaliser sur l\'équipement; selon les cas plusieurs choix sont disponibles: Créer, Modifier, Activer, Désactiver, Supprimer. La différence entre Désactiver et Supprimer tient dans le fait qu\'un équipement désactivé pourra être réactivé rapidement sans paramétrage supplémentaire alors qu\'un équipement supprimé devra faire l\'objet d\'un reparamétrage complet. La désactivation est à privilégier si l\'équipement ne doit plus être supervisé pour une période assez longue pour ensuite être réactivé. Si un serveur doit être reconfiguré (réinstallation de l\'OS par ex.) il convient de demander la désactivation du premier et la création du nouveau; le paramétrage des sondes étant différents selon les OS.\')">Actions à effectuer <img alt="point_interrogation" src="images/point-interrogation-16.png"></legend>';
		echo '<select id="Hote_action' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Action" class="hote' . $NbFieldset . '">';
			echo '<option value="Creer">A créer</option>';
		echo '</select><br />';
		echo '<!-- Commentaire -->';
		echo '<label for="Hote_Commentaire' . $NbFieldset . '" onclick="alert(\'Indiquez ici tout complément d\'information pouvant être utile à la configuration et mise en surveillance de l\'hôte.\')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<textarea id="Hote_Commentaire' . $NbFieldset . '" name="Hote_' . $NbFieldset . '_Commentaire" rows="2" cols="50" class="hote' . $NbFieldset . '"></textarea>';
		echo '<br />';
	echo '</fieldset>';

	echo '<span id="bouton_Hote' . $NbFieldset . '">';
		echo '<button id="Valider_Hote' . $NbFieldset . '" onclick="valider_fieldset_hote(this)" hidden="hidden">Valider</button>';
		echo '<!-- => doit ajouter automatiquement les services par défaut lié au modèle hote -->';
		echo '<button id="Cloner_Hote' . $NbFieldset . '" onclick="clone_fieldset_hote(this)">Dupliquer</button>';
		echo '<button id="Effacer_Hote' . $NbFieldset . '" onclick="efface_fieldset_hote(this)" hidden="hidden">Effacer</button>';
		echo '<button id="Supprimer_Hote' . $NbFieldset . '" onclick="supprime_fieldset_hote(this)">Retirer de la demande</button>';
		echo '<button id="PreEnregistrer_Hote' . $NbFieldset . '" onclick="PreEnregistrer_fieldset_hote(this)">Pré-Enregistrer cet hôte</button> <!-- => permet d\'enregistrer le nom de l\'hôte dans la table hote pour l\'avoir dispo dans les services -->';
	echo '</span>';
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Hote.php');
	};
echo '</fieldset>';
