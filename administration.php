<!DOCTYPE html>
<link rel="icon" href="./images/favicon.ico" />
<?php
session_start();
?>

<html>
<head>
	<?php
		include_once('top.php');
		include('head.php');
		include('log.php'); // chargement de la fonction de log
		addlog("chargement page administration.");
	?>
	<title>Administration changement - Tessi Technologies</title>

</head>
<body>
<div id="principal">
	<header id="en-tete">
		<?php
			include_once('menu.php');
		?>
	</header>
	<section>
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Gestion des modèles de service</a></li>
				<li><a href="#tabs-2">Association des modèles</a></li>
 				<li><a href="#tabs-3">Gestion des notifications BAM</a></li>
			</ul>
			<div id="tabs-1">
<!--				<h2>Modèles de service</h2> -->
				<fieldset id="Admin_Modele_Service" class="Admin_Modele_Service">
				<legend>Modèles de service</legend>
				<!-- Nom Modele Service -->
					<label for="Modele_Service">Nom du Modèle:</label>
					<?php 
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
							echo '<option value="' . htmlspecialchars($liste_modele_service['ID_Modele_Service']) . '">'. htmlspecialchars($liste_modele_service['Modele_Service']).'</option>';
						};
						echo '</select>';
						?>
					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Modele_Service" />
					<span id="Modele_Service_new" style="visibility: hidden;">
						<input onblur="verifChamp(this)" type="text" name="iModele_Service_new" id="iModele_Service_new" value="" placeholder="saisir le nom du modèle de service" size="30"/>
						<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_iModele_Service_new" />
						<label for="iModele_Service_Macro_new">Type Macro:</label>
						<input type="checkbox" name="iModele_Service_Macro_new" id="iModele_Service_Macro_new"/>
					</span>
					<button id="Ajouter_Argument">Ajouter</button><br />

				<!-- Commentaire -->
					<label for="Modele_Service_Description">Description :</label>
					<input onblur="verifChamp(this)" type="text" name="Modele_Service_Description" id="Modele_Service_Description" value="" placeholder="description brève du script" size="100"/>
					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Modele_Service_Description" /> <br />


<!-- Nombre arguments -->
<!--					<label for="Nb_Argument_Service">Nbre d'arguments:</label>
					<input type="text" id="Nb_Argument_Service" name="Nb_Argument_Service" onblur="verifChamp(this)" value="" size="5" class="verif"/>
					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_Nb_Argument_Service" />
-->
					<div id="argument_modele">
						<?php 
							include_once('modele_Modele_Argument_Service.php');
						?>
					</div>
					<button id="Enregistrer_Modele" onclick="enregistre_Modele();">Enregistrer</button>
					<button id="Supprimer_Modele">Supprimer le modèle</button><br />
				</fieldset>

			</div>
			<div id="tabs-2">
<!--				<h2>Association des modèles</h2> -->
					<fieldset id="Admin_Relation_Modeles" class="Admin_Relation_Modeles">
					<legend>Association des modèles Centreon</legend>
				<!-- Bouton Mise à jour des modèles centreon -->
<!--					<button id="MAJ_Modele_Centreon">Mise à jour des modèles Centreon</button> -->
				<!-- Nom Modele Service -->
					<label for="RM_Service">Nom du Modèle:</label>
					<?php
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
					?>
<!--					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_RM_Service" /> <br /> -->

				<!-- Liste Modele Service associés-->
					<div id="Liste_Associe">
<!--						<label for="Mod_Service_Associes">Liste des modèles associés:</label>
						<select id="Mod_Service_Associes" name="liste_associes[]" size="10" multiple>
						</select> <br /> 
						<button id="Dissocier_Selection">Dissocier la sélection =></button>
-->
					</div> 
				<!-- Liste Modele Service Centreon-->
					<div id="Liste_Centreon">
<!--						<label for="Mod_Service_Centreon">Liste des modèles centreon:</label>
						<select id="Mod_Service_Centreon" name="liste_modele[]" size="10" multiple>
						</select> <br />
						<button id="Associer_Selection"><= Associer la sélection</button>
-->
					</div>

				</fieldset>
				<fieldset id="Admin_Modele_Centreon" class="Admin_Modele_Centreon">
				<legend>Liste des modèles Centron non associés</legend>
					<?php
					try {
						include_once('requete_liste_Modele_Centreon_NonAssocies.php');
					} catch (Exception $e) {
						die ('Erreur requete liste modele centreon non associés' . $e->getMessage());
					};
					
					$nb_ligne = $req_modele_nonassocies->rowCount();
					//echo '<button id="MAJ_Modele_Centreon">Mise à jour des modèles Centreon</button>';
					echo '<table id="Liste_Modele_Centreon_NonAssocies">';
					echo '<tr>';
					//echo '<th>Modèle Centreon non associés (' . $nb_ligne . ')</th>';
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
						
					?>
				</fieldset>
			</div>
			<div id="tabs-3">
 				<h2>Gestion des notifications BAM</h2>

				<div id="accordion_bam_notifications">
					<h3>Liste des notifications BAM</h3>
					<div id="liste_notifications_bam">
						<?php 
							include_once('BAM_liste_notifications.php');														
						?> 
					</div>
					<h3>Configuration des notifications BAM</h3>
					<div id="config_notifications_bam">
						<!-- Bouton Ajout notification -->
 					<button id="config_notification" onclick="config_notification();">Configurer nouvelle notification</button>

						<fieldset id="field_config_notification" class="config_notification_bam">
						<?php 
  							include_once('BAM_config_notifications.php');
  						?>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</section>
	<footer>
		<?php
			include_once('PiedDePage.php');
		?>
	</footer>
</div>
	<!-- section des script javascript -->
	<?php
		include_once('section_script_JS.php');
	?>
</body>
</html>
