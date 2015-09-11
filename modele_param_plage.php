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
$NbFieldset_plage = (isset($_POST["NbFieldset_plage"])) ? $_POST["NbFieldset_plage"]+1 : 1;
$_SESSION['PDF'] = "Non";
$_SESSION['R_ID_Demande'] = NULL;
?>
<fieldset id="Plage<?php echo $NbFieldset_plage;?>" class="plage">
<legend>Plage horaire n°<?php echo $NbFieldset_plage;?></legend>

	<div id="model_param_plage">
<!-- Nom_Période -->
		<label for="Nom_Plage<?php echo $NbFieldset_plage;?>" class="jour" 
			onclick="alert('Saisir un nom pour identifier cette plage de surveillance.\nCette période sera disponible dans le paramétrage des services lorsque vous l\'aurez pré-enregistrée.\nLe nom définitif pourra être modifié selon les besoins (par ex. doublon de nom) lors du paramétrage dans Centreon.')">Nom de la plage horaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Nom_Plage<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Nom" onblur="verifChamp(this)" value="Plage par défaut" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nom_Plage<?php echo $NbFieldset_plage;?>"/> <br />

<!-- Lundi -->
		<label for="Lundi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Lundi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Lundi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Lundi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Lundi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Mardi -->
		<label for="Mardi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Mardi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Mardi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Mardi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Mardi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Mercredi -->
		<label for="Mercredi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Mercredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Mercredi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Mercredi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Mercredi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Jeudi -->
		<label for="Jeudi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Jeudi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Jeudi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Jeudi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Jeudi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Vendredi -->
		<label for="Vendredi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Vendredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Vendredi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Vendredi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Vendredi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Samedi -->
		<label for="Samedi<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Samedi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Samedi<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Samedi" onblur="verifChamp(this)" value="10:00-19:00" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Samedi<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Dimanche -->
		<label for="Dimanche<?php echo $NbFieldset_plage;?>" class="jour" onclick="alert('Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.')">Dimanche <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<input type="text" id="Dimanche<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Dimanche" onblur="verifChamp(this)" value="-" size="30" maxlength="30" class="plage<?php echo $NbFieldset_plage;?>"/>
		<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Dimanche<?php echo $NbFieldset_plage;?>" ondblclick="deverouille_liste(this)"/> <br />

<!-- Commentaire -->
		<label for="Commentaire_Plage<?php echo $NbFieldset_plage;?>" onclick="alert('Indiquez ici toute information complémentaire que vous n\'auriez pas sû transcrire ci-dessus.')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
		<textarea id="Commentaire_Plage<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Commentaire" rows="3" cols="50" class="plage<?php echo $NbFieldset_plage;?>"> </textarea> <br />

		<fieldset id="Action_Plage<?php echo $NbFieldset_plage;?>" class="plage_action">
		<legend>Actions à effectuer</legend>
			<select id="Plage_action<?php echo $NbFieldset_plage;?>" name="Plage_<?php echo $NbFieldset_plage;?>_Action" class="plage<?php echo $NbFieldset_plage;?>">
				<option Selected="Selected" value="Creer">A créer</option>
			</select>
		</fieldset>
		<span id="bouton_Plage<?php echo $NbFieldset_plage;?>">
			<button id="Cloner_Plage<?php echo $NbFieldset_plage;?>" onclick="clone_fieldset_Plage(this)">Dupliquer</button>
			<button id="Effacer_Plage<?php echo $NbFieldset_plage;?>" onclick="efface_fieldset_Plage(this)" hidden="hidden">Effacer</button>
			<button id="Supprimer_Plage<?php echo $NbFieldset_plage;?>" onclick="supprime_fieldset_Plage(this)">Retirer de la demande</button>
			<button id="PreEnregistrer_Plage<?php echo $NbFieldset_plage;?>" onclick="PreEnregistrer_fieldset_plage(this)">Pré-Enregistrer cette plage</button>  <!-- => permet d'enregistrer le nom de la plage dans la liste des périodes dispo pour configurer les services -->
		</span> <br />
	<?php
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Plage.php');
	};
	?>
	</div>
</fieldset>
