<?php
if (session_id()=='')
{
session_start();
};
if ($_SESSION['ID_dem'] == 0)
{
	echo '<p>Vous devez saisir les informations générales et valider votre sélection sur les onglets précédents avant de passer au paramétrage!</p>';
	return False;
};
$ID_Demande = $_SESSION['ID_dem'];
$NbFieldset_Service = (isset($_POST["NbFieldset_Service"])) ? $_POST["NbFieldset_Service"]+1 : 1;
$_SESSION['PDF'] = false;
$_SESSION['R_ID_Demande'] = NULL; // sur un ajout on force systématiquement à NULL

?>
<fieldset id="Service<?php echo $NbFieldset_Service;?>" class="service">
<legend>Service n°<?php echo $NbFieldset_Service;?></legend>

<!-- Nom service -->
	<label for="Nom_Service<?php echo $NbFieldset_Service;?>">Nom de la sonde :</label>
	<input type="text" id="Nom_Service<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Nom" onblur="verifChamp(this);verifNom_Service(this)" value="" size="40" maxlength="100" class="service<?php echo $NbFieldset_Service;?>"/>
	<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nom_Service<?php echo $NbFieldset_Service;?>" />

<!-- Hote du service -->
	<label for="Hote_Service<?php echo $NbFieldset_Service;?>">Hôte de la sonde:</label>
	<select id="Hote_Service<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Hote" onblur="verifChamp(this)" class="service<?php echo $NbFieldset_Service;?>">  <!-- Liste Hote disponibles -->
		<option value="" selected >...</option> <!-- Valeur par défaut -->
		<?php
			include('connexion_sql_supervision.php'); 
			try {
				include_once('requete_liste_Service_Hote.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete liste service_hote: ' . $e->getMessage());
			};
			while ($res_Service_H = $req_Service_Hote->fetch())
			{
		?>
<!-- 		<option value="<?php echo htmlspecialchars($res_Service_H['Nom_Hote']) ?>"><?php echo htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) ?></option> -->
			<option value="<?php echo htmlspecialchars($res_Service_H['ID_Hote']) ?>"><?php echo htmlspecialchars($res_Service_H['Nom_Hote']) . ' - ' . htmlspecialchars($res_Service_H['IP_Hote']) ?></option>
		<?php
			}
		?>
	</select>
	<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Hote_Service<?php echo $NbFieldset_Service;?>"/> <br />

<!-- Plage Horaire -->
	<label for="Service_Plage<?php echo $NbFieldset_Service;?>">Plage horaire de contrôle :</label>
	<select id="Service_Plage<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Plage_Horaire" onblur="verifChamp(this)" class="service<?php echo $NbFieldset_Service;?>">  <!-- Liste Service_Plage -->
		<option value="" selected >...</option> <!-- Valeur par défaut -->
		<?php
			//include_once('connexion_sql_supervision.php'); 
			try {
				include_once('requete_liste_Service_Plage.php');
			} catch (Exception $e) {
				echo '</select>';
				die('Erreur requete liste service_Plage: ' . $e->getMessage());
			};
			while ($res_Service_P = $req_Service_Plage->fetch())
			{ 
		?>
		<option value="<?php echo htmlspecialchars($res_Service_P['Nom_Periode']) ?>"><?php echo htmlspecialchars($res_Service_P['Nom_Periode'])?></option> 
		<?php
			} 
		?>
	</select>
	<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Plage<?php echo $NbFieldset_Service;?>" ondblclick="deverouille_liste(this)"/>

<!-- Controle_Actif -->
	<label for="Service_Actif<?php echo $NbFieldset_Service;?>">Contrôle :</label>
	<input Disabled="Disabled" type="text" id="Service_Actif<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Controle" onblur="verifChamp(this)" value="actif" size="5" class="service<?php echo $NbFieldset_Service;?>"/>
	<input Disabled="Disabled" style="visibility: hidden" type="text" id="Service_ID_Hote_Centreon<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_ID_Hote_Centreon" value="0" size="5" class="service<?php echo $NbFieldset_Service;?>"/><br/>
	
<!-- Modele service -->
	<label for="Service_Modele<?php echo $NbFieldset_Service;?>">Modèle :</label>
	<select id="Service_Modele<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Modele" onChange="afficher_argument('<?php echo $NbFieldset_Service;?>')" onblur="verifChamp(this)" class="service<?php echo $NbFieldset_Service;?>"> 
		<option value="" selected >...</option> <!-- Valeur par défaut -->
		<?php
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
		?>
	</select>
	<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Service_Modele<?php echo $NbFieldset_Service;?>" ondblclick="deverouille_liste(this)"/>

<!-- Frequence -->
	<label for="Frequence_Service<?php echo $NbFieldset_Service;?>">Fréquence du contrôle :</label>
	<input type="text" id="Frequence_Service<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Frequence" onblur="verifChamp(this)" value="Par défaut" size="20" maxlength="20" class="service<?php echo $NbFieldset_Service;?>" title="Fréquence des principaux contrôles: Disque 30 minutes; Programmes, répertoires et sites web 5 minutes; Controles Vacation Bosmanager 15 minutes; controle Teleco 30 minutes; Controle certificats 1 fois par jour"/>
	<img src="images/img_ok.png" class="verif" alt="correct" id="img_Frequence_Service<?php echo $NbFieldset_Service;?>" ondblclick="deverouille_liste(this)"/><br />
	
	<fieldset id="Arg_Service_Modele<?php echo $NbFieldset_Service;?>"> 
		<?php
		$Description = "Sélectionner un modèle ci-dessus";
		$nbLibelle = 1;
		$T_Libelle[0] = "Libellé 1";
		$T_Argument_Mod[0] = "Argument 1";
		$T_Argument[0] = "";
		$Num_Argument = 1;
	
		include('gestion_affichage_arguments.php');
		?>
	</fieldset><br />

<!-- Service Consigne -->
<!-- 	<label for="Service_Consigne<?php //echo $NbFieldset_Service;?>">Lien vers la consigne :</label> -->
<!-- 	<input type="text" id="Service_Consigne<?php //echo $NbFieldset_Service;?>" name="Service_<?php //echo $NbFieldset_Service;?>_Lien_Consigne" value="" size="70" maxlength="255" class="service<?php //echo $NbFieldset_Service;?>"/> <br /> -->
	<span id="Service_Consigne<?php echo $NbFieldset_Service;?>" class="service<?php echo $NbFieldset_Service;?>">Lien vers la consigne :<a id="Service_Consigne_lien<?php echo $NbFieldset_Service;?>" href="" target="_blank"></a></span>	<br />
	
<!-- Service Consigne Description -->
	<label for="Consigne_Service_Detail<?php echo $NbFieldset_Service;?>" onclick="alert('Décrivez ici les opérations à effectuer par les équipes EPI et/ou CDS si un évènement se produit sur l\'équipement (relancer un process, envoyer un mail, etc...).\nLes consignes doivent être claires et précises afin qu\'elles puissent être appliquées rapidement et sans ambiguïté par les équipes de support.\nLes adresses mails doivent être indiquées en toute lettre soit par ex: envoyer un mail à support_bmd@tessi.fr et pas simplement envoyer un mail support bmd.\nCette consigne sera ensuite retranscrite dans le wiki tessi-techno et un lien sera rattaché à l\'hôte; le lien apparaitra par la suite dans le champ ci-dessus.')">Description consigne <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
	<textarea id="Consigne_Service_Detail<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Description_Consigne" onblur="verifChamp(this)" rows="3" cols="50" class="service<?php echo $NbFieldset_Service;?>"></textarea>
	<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Consigne_Service_Detail<?php echo $NbFieldset_Service;?>" ondblclick="deverouille_liste(this)"/><br />
	
<!-- Action à effectuer -->
	<fieldset id="Action_Service<?php echo $NbFieldset_Service;?>" class="service_action">
		<legend>Actions à effectuer</legend>
		<select id="Service_action<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Action" class="service<?php echo $NbFieldset_Service;?>">
			<option value="Creer">A créer</option>
		</select> <br />
<!-- Service Commentaire -->
		<label for="Service_Commentaire<?php echo $NbFieldset_Service;?>" onclick="alert('Indiquez ici toute information complémentaire utile au paramétrage; Dans cette zone vous pouvez également indiquer le nouveau nom du service s\'il doit être changé.')">Commentaire  <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<textarea id="Service_Commentaire<?php echo $NbFieldset_Service;?>" name="Service_<?php echo $NbFieldset_Service;?>_Commentaire" rows="3" cols="50" class="service<?php echo $NbFieldset_Service;?>"></textarea> <br />

	</fieldset>

	<span id="bouton_Service<?php echo $NbFieldset_Service;?>" >
		<button id="Cloner_Service<?php echo $NbFieldset_Service;?>" onclick="clone_fieldset_service(this)">Dupliquer</button>
		<button id="Effacer_Service<?php echo $NbFieldset_Service;?>" onclick="efface_fieldset_service(this)" hidden="hidden">Effacer</button>
		<button id="Supprimer_Service<?php echo $NbFieldset_Service;?>" onclick="supprime_fieldset_service(this)">Retirer de la demande</button>
	</span>
	<?php
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Service.php');
	};
	?>
</fieldset>