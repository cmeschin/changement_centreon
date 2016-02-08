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
$_SESSION['R_ID_Demande'] = NULL;

?>
<fieldset id="Hote<?php echo $NbFieldset;?>" class="hote">
	<legend>Hôte n°<?php echo $NbFieldset;?></legend>

	<div id="model_param_hote">
		<!-- Hote -->
		<label for="Nom_Hote<?php echo $NbFieldset;?>" class="hote_Nom_IP" onclick="alert('Saisir le nom de l\'hôte tel que définit dans les propriétés systèmes.\nCaractères alphanumériques, tirets haut et bas uniquement.')">Nom
			de l'hôte <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <input type="text"
			id="Nom_Hote<?php echo $NbFieldset;?>"
			name="Hote_<?php echo $NbFieldset;?>_Nom" onblur="verifChamp(this);majuscule(this);verifChampNonIP(this);verifNom_Hote(this)"
			value="" size="20" maxlength="20"
			class="hote<?php echo $NbFieldset;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect"
			id="img_Nom_Hote<?php echo $NbFieldset;?>" /> <br />

		<!-- Adresse IP -->
		<label for="IP_Hote<?php echo $NbFieldset;?>" class="hote_Nom_IP">Adresse
			IP :</label> <input type="text" id="IP_Hote<?php echo $NbFieldset;?>"
			name="Hote_<?php echo $NbFieldset;?>_IP" onblur="verifChampIP(this)"
			value="" class="hote<?php echo $NbFieldset;?>"/> <img src="images/img_edit.png"
			class="verif" alt="incorrect"
			id="img_IP_Hote<?php echo $NbFieldset;?>" />
	</div>

	<!-- Description -->
	<div id="model_param_hote">
		<label for="Hote_Description<?php echo $NbFieldset;?>"
			class="hote_Nom_IP" onclick="alert('Saisir ici une description succinte de l\'hôte. Pour les routeurs, switch, firewall, etc... merci d\'indiquer le modèle (SRX2200, HP Procurve, 24/48 ports, etc...).')">Description <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br />
		<textarea id="Hote_Description<?php echo $NbFieldset;?>"
			name="Hote_<?php echo $NbFieldset;?>_Description" rows="2" cols="40"
			onblur="verifChamp(this)" class="hote<?php echo $NbFieldset;?>"></textarea>
		<img src="images/img_edit.png" class="verif" alt="incorrect"
			id="img_Hote_Description<?php echo $NbFieldset;?>" />
	</div>
	<br />

	<!-- Localisation -->
	<label for="Localisation<?php echo $NbFieldset;?>" onclick="alert('Indiquez la localisation géographique de l\'hôte, si elle n\'apparait pas dans la liste, sélectionnez Autre et indiquez le nouveau site.')">Localisation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<select	id="Localisation<?php echo $NbFieldset;?>"
		name="Hote_<?php echo $NbFieldset;?>_Localisation"
		onChange="afficher_autre(this)" onblur="verifChamp(this)"
		class="hote<?php echo $NbFieldset;?>">
		<!-- Liste Localisation -->
		<option value="" selected>...</option>
		<!-- Valeur par défaut -->
		<option value="Autre">Autre</option>
		<!-- Valeur à sélectionner pour en créer un -->
		<?php
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
		?>
	</select> <img src="images/img_edit.png" class="verif" alt="incorrect"
		id="img_Localisation<?php echo $NbFieldset;?>" /> <span
		id="Localisation<?php echo $NbFieldset;?>_new"
		style="visibility: hidden;"> <input onblur="verifChamp(this)"
		type="text" name="Localisation<?php echo $NbFieldset;?>_new"
		id="Localisation<?php echo $NbFieldset;?>_new" value="Vide"
		placeholder="par ex: Nanterre" size="20"
		class="hote<?php echo $NbFieldset;?>"
		title="Saisir le nouveau site... Le nom final pourra être modifié afin de correspondre à la règle de nommage." />
		<img src="images/img_ok.png" class="verif" alt="correct"
		id="img_Localisation<?php echo $NbFieldset;?>_new" />
	</span> <br />

	<!-- Type Hote -->
	<label for="Type_Hote<?php echo $NbFieldset;?>" onclick="alert('Sélectionnez le type d\'hôte dans la liste.\nCette information fait partie de la règle de nommage des hôtes dans Centreon.')">Type <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<select
		id="Type_Hote<?php echo $NbFieldset;?>"
		name="Hote_<?php echo $NbFieldset;?>_Type"
		onChange="afficher_autre(this)" onblur="verifChamp(this)"
		class="hote<?php echo $NbFieldset;?>">
		<!-- Liste Type_Hote -->
		<option value="" selected>...</option>
		<!-- Valeur par défaut -->
		<option value="Autre">Autre</option>
		<!-- Valeur à sélectionner pour en créer un -->
		<?php
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
		?>
	</select> <img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Type_Hote<?php echo $NbFieldset;?>" /> 
		<span id="Type_Hote<?php echo $NbFieldset;?>_new"
		style="visibility: hidden;"> <input onblur="verifChamp(this)"
		type="text" name="Type_Hote<?php echo $NbFieldset;?>_new"
		id="Type_Hote<?php echo $NbFieldset;?>_new" value="Vide"
		placeholder="saisir le nouveau type d'hôte..." size="30"
		class="hote<?php echo $NbFieldset;?>"
		title="Saisissez le nouveau type d'hôte... Le nom final pourra être modifié afin de correspondre à la règle de nommage." />
		<img src="images/img_ok.png" class="verif" alt="correct" id="img_Type_Hote<?php echo $NbFieldset;?>_new" />
	</span> <br />

	<!-- OS -->
	<label for="Type_OS<?php echo $NbFieldset;?>" onclick="alert('Sélectionnez le système d\'exploitation installé sur l\'hôte. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]')">Système d'exploitation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<select id="Type_OS<?php echo $NbFieldset;?>"
		name="Hote_<?php echo $NbFieldset;?>_OS"
		onChange="afficher_autre(this)"
		onblur="verifChamp(this)" class="hote<?php echo $NbFieldset;?>">
		<!-- Liste Type_OS -->
		<option value="" selected>...</option>
		<!-- Valeur par défaut -->
		<option value="NC">Non Concerné</option>
		<!-- Valeur si Non Concerné -->
		<option value="Autre">Autre</option>
		<!-- Valeur à sélectionner pour en créer un -->
		<?php
		// include_once('connexion_sql_supervision.php');
		try {
			include_once ('requete_liste_Hote_OS.php');
		} catch (Exception $e) {
			echo '</select>';
			die ('Erreur requete_liste_hote_OS' . $e->getMessage());
		};
		while ( $res_OS = $req_OS->fetch () ) {
			?>
		<option value="<?php echo htmlspecialchars($res_OS['Type_OS']) ?>"><?php echo htmlspecialchars($res_OS['Type_OS']) ?></option> 
		<?php
		}
		?>
	</select> <img src="images/img_edit.png" class="verif" alt="incorrect"
		id="img_Type_OS<?php echo $NbFieldset;?>" /> <span
		id="Type_OS<?php echo $NbFieldset;?>_new" style="visibility: hidden;">
		<input onblur="verifChamp(this)" type="text"
		name="Type_OS<?php echo $NbFieldset;?>_new"
		id="Type_OS<?php echo $NbFieldset;?>_new" value="Vide"
		placeholder="par ex: Ubuntu 14.04 ou Windows 9" size="30"
		class="hote<?php echo $NbFieldset;?>"
		title="Indiquez le système d'exploitation installé sur l'hôte." />
		<img src="images/img_ok.png" class="verif" alt="correct"
		id="img_Type_OS<?php echo $NbFieldset;?>_new" />
	</span> <br />

	<!-- Architecture -->
	<label for="Architecture<?php echo $NbFieldset;?>" onclick="alert('Indiquez s\'il s\'agit d\'un OS 32 ou 64 bits. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]')">Architecture <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<select id="Architecture<?php echo $NbFieldset;?>"
		name="Hote_<?php echo $NbFieldset;?>_Architecture"
		onChange="afficher_autre(this)" onblur="verifChamp(this)"
		class="hote<?php echo $NbFieldset;?>">
		<!-- Liste Architecture -->
		<option value="" selected>...</option>
		<!-- Valeur par défaut -->
		<option value="NC">Non Concerné</option>
		<!-- Valeur si Non Concerné -->
		<option value="32_bits">32_bits</option>
		<option value="64_bits">64_bits</option>
	</select> <img src="images/img_edit.png" class="verif" alt="incorrect"
		id="img_Architecture<?php echo $NbFieldset;?>" />

	<!-- Langue -->
	<label for="Langue<?php echo $NbFieldset;?>" onclick="alert('Sélectionnez la langue du système d\'exploitation installé sur l\'hôte. Pour les hôtes de type ESX, Routeur, Switch, Firewall, etc... sélectionnez [Non concerné]')">Langue <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	 <select name="Langue<?php echo $NbFieldset;?>"
		id="Langue<?php echo $NbFieldset;?>" onChange="afficher_autre(this)"
		onblur="verifChamp(this)" class="hote<?php echo $NbFieldset;?>">
		<!-- Liste Langue -->
		<option value="" selected>...</option>
		<!-- Valeur par défaut -->
		<option value="NC">Non Concerné</option>
		<!-- Valeur si Non Concerné -->
		<option value="Francais">Français</option>
		<option value="Anglais">Anglais</option>
	</select> <img src="images/img_edit.png" class="verif" alt="incorrect"
		id="img_Langue<?php echo $NbFieldset;?>" />

	<!-- Fonction -->
	<label for="Fonction<?php echo $NbFieldset;?>" onclick="alert('Indiquez la ou les fonctions principales de l\'hôte. Cette information permettra de catégoriser l\'équipement.\nLes fonctions principales sont les suivantes: BMChèque, BMDocument, Marcel, Tri, Videocodage, ICR, CFT, IBML, Fax, Parefeu, etc...')">Fonction(s) <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <input
		type="text" id="Fonction<?php echo $NbFieldset;?>"
		name="Fonction<?php echo $NbFieldset;?>" value="" size="30"
		placeholder="par ex: Fax, Routeur, BMChèque, BosVideo, Marcel, etc..."
		maxlength="50" class="hote<?php echo $NbFieldset;?>"/>

	<br />
	<!-- Consigne -->
<!-- 	<label for="Consigne_Hote<?php //echo $NbFieldset;?>" onclick="alert('Indiquez ici le lien vers une consigne du wiki.\nLes consignes ont pour but de fournir les indications nécessaires et suffisantes quant aux actions\nà réaliser par les équipes EPI et/ou CDS si un évènement se produit sur l\'équipement (relance d\'un process, envoi de mail, etc...)')">Lien vers consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> -->
<!-- 	<input style="visibility: hidden;" type="text" id="Consigne_Hote<?php //echo $NbFieldset;?>" name="Hote_<?php //echo $NbFieldset;?>_Consigne" value="" size="90"	maxlength="255" class="hote<?php //echo $NbFieldset;?>"/> -->
	<span id="Consigne_Hote<?php echo $NbFieldset;?>" class="hote<?php echo $NbFieldset;?>">Lien vers la consigne :<a href="" target="_blank"></a></span>
	<br />

	<!-- Detail consigne -->
	<label for="Consigne_Hote_Detail<?php echo $NbFieldset;?>" onclick="alert('Décrivez ici les opérations à effectuer par les équipes EPI et/ou CDS si un évènement se produit sur l\'équipement (relancer un process, envoyer un mail, etc...). Les consignes doivent être claires et précises afin qu\'elles puissent être appliquées rapidement et sans ambiguïté par les équipes de support. Les adresses mails doivent être indiquées en toute lettre soit par ex: envoyer un mail à support_bmd@tessi.fr et pas simplement envoyer un mail support bmd. Cette consigne sera ensuite retranscrite dans le wiki tessi-techno et un lien sera rattaché à l\'hôte.')">Description
		consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<textarea id="Consigne_Hote_Detail<?php echo $NbFieldset;?>"
		name="Hote_<?php echo $NbFieldset;?>_Description_Consigne" onblur="verifChamp(this)" rows="3"
		cols="50" class="hote<?php echo $NbFieldset;?>"></textarea>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Consigne_Hote_Detail<?php echo $NbFieldset;?>" ondblclick="deverouille_liste(this)"/>
	<!-- <br /> -->

	<!-- Controle_actif -->
	<label for="Controle_Actif_Hote<?php echo $NbFieldset;?>"
		sstyle="visibility: hidden">Controle :</label> <input type="text"
		id="Controle_Actif_Hote<?php echo $NbFieldset;?>"
		name="Controle_Actif<?php echo $NbFieldset;?>" readonly value="actif"
		size="5" sstyle="visibility: hidden"
		class="hote<?php echo $NbFieldset;?>" /> <br />

	<!-- Action à  effectuer -->
	<fieldset id="Action_Hote<?php echo $NbFieldset;?>" class="hote_action">
		<legend onclick="alert('Sélectionnez l\'action à réaliser sur l\'équipement; selon les cas plusieurs choix sont disponibles: Créer, Modifier, Activer, Désactiver, Supprimer. La différence entre Désactiver et Supprimer tient dans le fait qu\'un équipement désactivé pourra être réactivé rapidement sans paramétrage supplémentaire alors qu\'un équipement supprimé devra faire l\'objet d\'un reparamétrage complet. La désactivation est à privilégier si l\'équipement ne doit plus être supervisé pour une période assez longue pour ensuite être réactivé. Si un serveur doit être reconfiguré (réinstallation de l\'OS par ex.) il convient de demander la désactivation du premier et la création du nouveau; le paramétrage des sondes étant différents selon les OS.')">Actions à effectuer <img alt="point_interrogation" src="images/point-interrogation-16.png"></legend>
		<select id="Hote_action<?php echo $NbFieldset;?>"
			name="Hote_<?php echo $NbFieldset;?>_Action"
			class="hote<?php echo $NbFieldset;?>">
			<option value="Creer">A créer</option>
		</select><br />
		<!-- Commentaire -->
		<label for="Hote_Commentaire<?php echo $NbFieldset;?>" onclick="alert('Indiquez ici tout complément d\'information pouvant être utile à la configuration et mise en surveillance de l\'hôte.')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<textarea id="Hote_Commentaire<?php echo $NbFieldset;?>"
			name="Hote_<?php echo $NbFieldset;?>_Commentaire" rows="2" cols="50"
			class="hote<?php echo $NbFieldset;?>"></textarea>
		<br />
	</fieldset>

	<span id="bouton_Hote<?php echo $NbFieldset;?>">
		<button id="Valider_Hote<?php echo $NbFieldset;?>"
			onclick="valider_fieldset_hote(this)" hidden="hidden">Valider</button>
		<!-- => doit ajouter automatiquement les services par défaut lié au modèle hote -->
		<button id="Cloner_Hote<?php echo $NbFieldset;?>"
			onclick="clone_fieldset_hote(this)">Dupliquer</button>
		<button id="Effacer_Hote<?php echo $NbFieldset;?>"
			onclick="efface_fieldset_hote(this)" hidden="hidden">Effacer</button>
		<button id="Supprimer_Hote<?php echo $NbFieldset;?>"
			onclick="supprime_fieldset_hote(this)">Retirer de la demande</button>
		<button id="PreEnregistrer_Hote<?php echo $NbFieldset;?>"
			onclick="PreEnregistrer_fieldset_hote(this)">Pré-Enregistrer cet
			hôte</button> <!-- => permet d'enregistrer le nom de l'hôte dans la table Hote_Temp pour l'avoir dispo dans les services -->
	</span>
	<?php
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Hote.php');
	};
	?>
</fieldset>
