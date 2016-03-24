<!DOCTYPE html>
<?php
//session_start();

echo '<html>';
echo '<head>';
		include_once('top.php');
		include_once('head.php');
		include('log.php'); // chargement de la fonction de log
		addlog("chargement page administration.");
	echo '<title>Administration changement - Tessi Technologies</title>';

echo '</head>';
echo '<body>';
echo '<div id="principal">';
	echo '<header id="en-tete">';
			include_once('menu.php');
	echo '</header>';
	echo '<section>';
		echo '<div id="tabs">';
			echo '<ul>';
				echo '<li><a href="#tabs-1">Gestion des modèles de service</a></li>';
				echo '<li><a href="#tabs-2">Association des modèles</a></li>';
 				echo '<li><a href="#tabs-3">Gestion des notifications BAM</a></li>';
 				echo '<li><a href="#tabs-4">Statistiques de traitement</a></li>';
			echo '</ul>';
			echo '<div id="tabs-1">';
				echo '<fieldset id="Admin_Modele_Service" class="Admin_Modele_Service">';
				echo '<legend>Modèles de service</legend>';
				echo '<!-- Nom Modele Service -->';
					echo '<label for="Modele_Service">Nom du Modèle:</label>';
						echo '<select name="Modele_Service" id="Modele_Service" onChange="charger_modele(this)" onblur="verifChamp(this)">  <!-- Liste Modele_Service -->';
						echo '<option value="" selected >...</option> <!-- Valeur par défaut -->';
						echo '<option value="Nouveau">Nouveau</option> <!-- Valeur à sélectionner pour en créer un -->';
						include_once('connexion_sql_supervision.php');
						try {
							include_once('requete_liste_Modele_Service.php');
						} catch (Exception $e) {
							echo '</select> <br />';
							die('Erreur requete liste modele service: ' . $e->getMessage());
						};
						while ($liste_modele_service = $req_modele->fetch())
						{
							echo '<option value="' . htmlspecialchars($liste_modele_service["ID_Modele_Service"]) . '">' . htmlspecialchars($liste_modele_service["Modele_Service"]) . '</option>';
						};
						echo '</select>';
					echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Modele_Service" />';
					echo '<span id="Modele_Service_new" style="visibility: hidden;">';
						echo '<input onblur="verifChamp(this)" type="text" name="iModele_Service_new" id="iModele_Service_new" value="" placeholder="saisir le nom du modèle de service" size="30"/>';
						echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_iModele_Service_new" />';
						echo '<label for="iModele_Service_Macro_new">Type Macro:</label>';
						echo '<input type="checkbox" name="iModele_Service_Macro_new" id="iModele_Service_Macro_new"/>';
					echo '</span>';
					echo '<button id="Ajouter_Argument">Ajouter</button><br />';

					echo '<!-- Commentaire -->';
					echo '<label for="Modele_Service_Description">Description :</label>';
					echo '<input onblur="verifChamp(this)" type="text" name="Modele_Service_Description" id="Modele_Service_Description" value="" placeholder="description brève du script" size="100"/>';
					echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Modele_Service_Description" /> <br />';
					echo '<div id="argument_modele">';
					include_once('modele_Modele_Argument_Service.php');
					echo '</div>';
					echo '<button id="Enregistrer_Modele" onclick="enregistre_Modele();">Enregistrer</button>';
					echo '<button id="Supprimer_Modele">Supprimer le modèle</button><br />';
				echo '</fieldset>';
			echo '</div>';
			echo '<div id="tabs-2">';
			echo '<fieldset id="Admin_Relation_Modeles" class="Admin_Relation_Modeles">';
			echo '<legend>Association des modèles Centreon</legend>';
					echo '<label for="RM_Service">Nom du Modèle:</label>';
					echo '<select name="RM_Service" id="RM_Service" onChange="charger_relation_modele()" class="verif">';
						echo '<option value="" selected >...</option>';
						//include_once('connexion_sql_supervision.php'); 
						try {
							include('requete_liste_Modele_Service.php');
						} catch (Exception $e) {
							echo '</select> <br />';
							die('Erreur requete liste modele service: ' . $e->getMessage());
						};
						while ($liste_modele_service = $req_modele->fetch())
						{ 
							echo '<option value="' . htmlspecialchars($liste_modele_service['ID_Modele_Service']) . '">'. htmlspecialchars($liste_modele_service['Modele_Service']) .'</option>'; 
						};
						echo '</select> <br />';

					echo '<div id="Liste_Associe">';
					echo '</div>';
					echo '<div id="Liste_Centreon">';
					echo '</div>';

				echo '</fieldset>';
				echo '<fieldset id="Admin_Modele_Centreon" class="Admin_Modele_Centreon">';
				echo '<legend>Liste des modèles Centron non associés</legend>';
					try {
						include_once('requete_liste_Modele_Centreon_NonAssocies.php');
					} catch (Exception $e) {
						die ('Erreur requete liste modele centreon non associés' . $e->getMessage());
					};
					
					$nb_ligne = $req_modele_nonassocies->rowCount();
					echo '<table id="Liste_Modele_Centreon_NonAssocies">';
					echo '<tr>';
					echo '<th><button id="MAJ_Modele_Centreon">Mise à jour des modèles Centreon</button> (' . $nb_ligne . ')</th>';
					echo '<th hidden>service_id</th>';
					echo '</tr>';
					while($res_modele_nonassocies = $req_modele_nonassocies->fetch())
					{
						echo '<tr>';
						echo '<td>' . htmlspecialchars($res_modele_nonassocies['service_description']) . '</td>';
						echo '<td hidden>' . htmlspecialchars($res_modele_nonassocies['service_id']) . '</td>';
					};
					echo '</tr>';
					echo '</table>';
						
				echo '</fieldset>';
			echo '</div>';
			echo '<div id="tabs-3">';
 				echo '<h2>Gestion des notifications BAM</h2>';

				echo '<div id="accordion_bam_notifications">';
					echo '<h3>Liste des notifications BAM</h3>';
					echo '<div id="liste_notifications_bam">';
							include_once('BAM_liste_notifications.php');														
					echo '</div>';
					echo '<h3>Configuration des notifications BAM</h3>';
					echo '<div id="config_notifications_bam">';
						echo '<!-- Bouton Ajout notification -->';
 						echo '<button id="config_notification" onclick="config_notification();">Configurer nouvelle notification</button>';
						echo '<fieldset id="field_config_notification" class="config_notification_bam">';
  							include_once('BAM_config_notifications.php');
						echo '</fieldset>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			echo '<div id="tabs-4">';
 				echo '<h2>Statistiques de traitement des demandes</h2>';
 				include_once('statistiques_traitement.php');
				echo '<div id="accordion_charge_temps">';
					echo '<h3>Charge horaire estimée par semaine</h3>';
					echo '<div id="charge_horaire">';
						echo '<img border="0" sstyle="width:30%" src="charge_temps.png">';														
					echo '</div>';
					echo '<h3>Charge volumétrique par semaine</h3>';
					echo '<div id="charge_volumetrique">';
						echo '<img border="0" sstyle="width:30%" src="charge_nombre.png">';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</section>';
	echo '<footer>';
			include_once('PiedDePage.php');
	echo '</footer>';
echo '</div>';
	echo '<!-- section des script javascript -->';
		include_once('section_script_JS.php');
echo '</body>';
echo '</html>';
