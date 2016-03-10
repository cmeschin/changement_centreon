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
$_SESSION['PDF'] = false;
$_SESSION['Extraction'] = false;

echo '<fieldset id="Plage' . $NbFieldset_plage . '" class="plage">';
echo '<legend>Plage horaire n°' . $NbFieldset_plage . '</legend>';

	echo '<div id="model_param_plage">';
echo '<!-- Nom_Période -->';
		echo '<label for="Nom_Plage' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisir un nom pour identifier cette plage de surveillance.\nCette période sera disponible dans le paramétrage des services lorsque vous l\'aurez pré-enregistrée.\nLe nom définitif pourra être modifié selon les besoins (par ex. doublon de nom) lors du paramétrage dans Centreon.\')">Nom de la plage horaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Nom_Plage' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Nom" onblur="verifChamp(this)" value="Plage par défaut" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nom_Plage' . $NbFieldset_plage . '"/> <br />';

echo '<!-- Lundi -->';
		echo '<label for="Lundi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Lundi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Lundi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Lundi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Lundi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Mardi -->';
		echo '<label for="Mardi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Mardi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Mardi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Mardi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Mardi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Mercredi -->';
		echo '<label for="Mercredi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Mercredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Mercredi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Mercredi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Mercredi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Jeudi -->';
		echo '<label for="Jeudi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Jeudi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Jeudi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Jeudi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Jeudi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Vendredi -->';
		echo '<label for="Vendredi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Vendredi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Vendredi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Vendredi" onblur="verifChamp(this)" value="07:00-21:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Vendredi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Samedi -->';
		echo '<label for="Samedi' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Samedi <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Samedi' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Samedi" onblur="verifChamp(this)" value="10:00-19:00" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Samedi' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Dimanche -->';
		echo '<label for="Dimanche' . $NbFieldset_plage . '" class="jour" onclick="alert(\'Saisissez la plage horaire avec le format suivant: 08:00-21:00 pour une surveillance de 8h à 21h.\nSi la plage de surveillance est discontinue séparez les plages par une virgule comme ceci: 08:00-11:45,12:45-21:00 pour une surveillance de 8h à 11h45 et de 12h45 à 21h.\nSaisissez simplement un tiret <-> pour les jours sans surveillance.\')">Dimanche <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<input type="text" id="Dimanche' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Dimanche" onblur="verifChamp(this)" value="-" size="30" maxlength="30" class="plage' . $NbFieldset_plage . '"/>';
		echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Dimanche' . $NbFieldset_plage . '" ondblclick="deverouille_liste(this)"/> <br />';

echo '<!-- Commentaire -->';
		echo '<label for="Commentaire_Plage' . $NbFieldset_plage . '" onclick="alert(\'Indiquez ici toute information complémentaire que vous n\'auriez pas sû transcrire ci-dessus.\')">Commentaire <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
		echo '<textarea id="Commentaire_Plage' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Commentaire" rows="3" cols="50" class="plage' . $NbFieldset_plage . '"> </textarea> <br />';

		echo '<fieldset id="Action_Plage' . $NbFieldset_plage . '" class="plage_action">';
		echo '<legend>Actions à effectuer</legend>';
			echo '<select id="Plage_action' . $NbFieldset_plage . '" name="Plage_' . $NbFieldset_plage . '_Action" class="plage' . $NbFieldset_plage . '">';
				echo '<option Selected="Selected" value="Creer">A créer</option>';
			echo '</select>';
		echo '</fieldset>';
		echo '<span id="bouton_Plage' . $NbFieldset_plage . '">';
			echo '<button id="Cloner_Plage' . $NbFieldset_plage . '" onclick="clone_fieldset_Plage(this)">Dupliquer</button>';
			echo '<button id="Effacer_Plage' . $NbFieldset_plage . '" onclick="efface_fieldset_Plage(this)" hidden="hidden">Effacer</button>';
			echo '<button id="Supprimer_Plage' . $NbFieldset_plage . '" onclick="supprime_fieldset_Plage(this)">Retirer de la demande</button>';
			echo '<button id="PreEnregistrer_Plage' . $NbFieldset_plage . '" onclick="PreEnregistrer_fieldset_plage(this)">Pré-Enregistrer cette plage</button>  <!-- => permet d\'enregistrer le nom de la plage dans la liste des périodes dispo pour configurer les services -->';
		echo '</span> <br />';
		if ($_SESSION['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		include('insere_fieldset_Admin_Plage.php');
	};
	echo '</div>';
echo '</fieldset>';
