<!DOCTYPE html>
<?php
if (session_id()=='')
{
	session_start();
};
$ID_Demande = (isset($_GET["id_demande"])) ? $_GET["id_demande"] : NULL;
$Demandeur = (isset($_GET["demandeur"])) ? $_GET["demandeur"] : NULL;
$_SESSION['Reprise'] = true;
$_SESSION['Nouveau'] = false;
$_SESSION['PDF'] = false;
$date=date_create();
$_SESSION['Timer']=date_timestamp_get($date);

include('log.php'); // chargement de la fonction de log
addlog("chargement reprise demande.");
?>
<html>
	<head>
		<?php
			include_once('top.php');
			include('head.php');
		?>
	</head>
	<body>
		<div id="principal">
			<header id="en-tete">
				<?php
					include_once('menu.php');
				?>
			</header>
			<section>
			<?php
			if (($Demandeur != $_SESSION['user_changement_centreon']) && ($_SESSION['Admin'] == false)) // tentative d'usurpation d'identité ;)
			{
				//addlog("##### ATTENTION tentative d'usurpation d'identité par " . $_SESSION['user_changement_centreon'] . "pour éditer la demande " . $ID_Demande . " de " . $Demandeur . ".");
				echo '<h3>Bonjour, ' . $_SESSION['name_changement_centreon'] . ' vous n\'êtes pas autorisé à éditer cette demande car vous n\'êtes ni administrateur ni l\'auteur de celle-ci!</h3>';
				$date_demande=date("d-m-Y H:i:s");
				echo '<p>nous sommes le ' . $date_demande . '</p>';
				echo '<p>Merci de choisir un menu ci dessus!</p>';
			} else // user= demandeur ou administrateur
			{
				// récupération des informations liées à la demande
				include_once('connexion_sql_supervision.php');
				try {
					include_once('requete_reprise_demande.php');
				} catch (Exception $e) {
					die('Erreur requete_reprise_demande: ' . $e->getMessage());
				};
							
				while ($res_Demande = $Select_Demande->fetch())
				{
					// déclaration des constantes de la demande
					$date_demande = htmlspecialchars($res_Demande['Date_Demande']);
					$ref_demande = htmlspecialchars($res_Demande['Ref_Demande']);
					$_SESSION['ref_dem'] = htmlspecialchars($res_Demande['Ref_Demande']);
					$_SESSION['ID_dem'] = htmlspecialchars($res_Demande['ID_Demande']);
					$_SESSION['Code_Client'] = htmlspecialchars($res_Demande['Code_Client']);
					addlog("Initialisation des constantes:");
					addlog("Date_demande:".$date_demande."");
					addlog("Ref_demande:".$ref_demande."");
					addlog("Prestation:".$res_Demande['Code_Client']."");
					
					?>	
					<!--	<fieldset class="liste_service_client">
							<legend>Liste des hôtes et services du client</legend> -->
					<!--		<h2 class="demoHeaders">Accordion</h2> -->
					<div id="tabs">
						 <ul>
							<li id="lien_tabs1"><a href="#tabs-1">Info générales</a></li>
							<li id="lien_tabs2" style="visibility: visible"><a href="#tabs-2" onclick="Enregistrer_Brouillon(false)">Liste hôtes et services</a></li>
							<li id="lien_tabs3" style="visibility: visible"><a href="#tabs-3">Paramétrage</a></li>
						</ul>
						<div id="tabs-1">
							<h2>Reprise - Informations générales sur la demande</h2>
							<fieldset id="info" class="info_generale">
								<span>
									<label for="demandeur">Demandeur :</label>
									<input readonly="readonly" type="text" id="demandeur" class="info_generale" name="demandeur" value="<?php echo htmlspecialchars($res_Demande['Demandeur']);?>" size="15"/>
									
									<label for="date_demande">Date de la demande :</label>
									<input readonly="readonly" type="text" id="date_demande" class="info_generale" name="date_demande" value="<?php echo $date_demande ;?>" size="15"/>
									
									<label for="ref_demande">Référence de la demande :</label>
									<input readonly="readonly" type="text" id="ref_demande" class="info_generale" name="ref_demande" value="<?php echo $ref_demande ;?>" size="20"/>
									<label for="etat_demande">Etat :</label>
									<input readonly="readonly" type="text" id="etat_demande" class="info_generale" name="etat_demande" value="<?php echo htmlspecialchars($res_Demande['Etat_Demande']);?>" size="10"/>
								</span> <br>									

								<label for="client" onclick="alert('Une fois que la demande est initialisée, il n\'est plus possible de changer la prestation.')" title="Cliquez pour plus d'informations.">Prestation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
								<?php
// désactivé le 12/09/15
// 									if (substr(htmlspecialchars($res_Demande['Code_Client']),0,4) == "NEW_")
// 									{
// 										echo '<select Disabled="Disabled" name="client" id="clientsup">  <!-- Liste Client -->';
// 										echo '<option Selected="Selected" value="Nouveau">Nouveau</option>';
// 									} else
// 									{
									echo '<select Disabled="Disabled" name="client" id="clientsup" class="info_generale">  <!-- Liste Client -->';
									echo '<option Selected="Selected" value="' . htmlspecialchars($res_Demande['Code_Client']) . '">' . htmlspecialchars($res_Demande['Code_Client']) . '</option>';
//									};
									echo '<select/>';
									//}; 
								?>
								<img src="images/img_ver.png" class="verif" alt="correct" id="img_client" />
								<?php
// 								if (substr(htmlspecialchars($res_Demande['Code_Client']),0,4) == "NEW_")
// 								{
// 									echo '<span id="sclient_new" style="visibility: visible;">';
// 										echo '<input readonly="readonly" onblur="verifChamp(this)" type="text" name="client_new" id="client_new" class="info_generale" value="' . substr(htmlspecialchars($res_Demande['Code_Client']),4) . '" placeholder="saisir le nom de la prestation..." size="50" maxlength="40" title="Saisissez le nom qui a été définit pour cette prestation lors du projet. Si vous ne le connaissez pas rapprochez vous du service qualité qui saura vous renseigner."/>';
// 										echo '<img src="images/img_ver.png" class="verif" alt="correct" id="img_client_new" />';
// 									echo '</span> <br />';
// 								} else // cela ne doit pas arriver!
// 								{
// 									echo '<span id="sclient_new" style="visibility: hidden;">';
// 										echo '<input onblur="verifChamp(this)" type="text" name="client_new" id="client_new" value="" placeholder="saisir le nom de la prestation..." size="50" maxlength="40" title="Saisissez le nom qui a été définit pour cette prestation lors du projet. Si vous ne le connaissez pas rapprochez vous du service qualité qui saura vous renseigner."/>';
// 										echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_client_new" />';
// 									echo '</span> <br />';
// 								};
//								?>

								<label for="date_livraison_demandee" onclick="alert('Indiquez la date à laquelle vous souhaiteriez que la supervision soit en place, idéalement la date de démarrage en production.')" title="Cliquez pour plus d'informations.">Date de supervision souhaitée <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
								<input disabled="disabled" readonly="readonly" type="text" name="date_livraison_demandee" class="info_generale" id="date_livraison_demandee" value="<?php echo htmlspecialchars($res_Demande['Date_Supervision_Demandee']);?>" size="10"/>
								<img src="images/img_ok.png" class="verif" alt="correct" id="img_date_livraison_demandee" /> <br />
								
								<label for="email" onclick="alert('Saisissez ici les emails des personnes qui devront être notifiées de la demande, centreon_tt est automatiquement notifié de la demande.\nSéparez les adresses par un point-virgule.')" title="Cliquez pour plus d'informations.">Liste des personnes à notifier <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
								<input type="text" id="email" class="info_generale" name="email" value="<?php echo htmlspecialchars($res_Demande['email']) ;?>" onblur="verifChampMail(this)" placeholder="séparer les adresses par un point-virgule" size="100"/>
								<img src="images/img_ok.png" class="verif" alt="correct" id="img_email" /> <br/>
			
								<label for="commentaire" onclick="alert('Saisissez ici toute information complémentaire susceptible d\'être utile au paramétrage de la supervision')" title="Cliquez pour plus d'informations.">Commentaires <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br/>
								<textarea id="commentaire" name="commentaire" class="info_generale" rows="5" cols="100"><?php echo htmlspecialchars($res_Demande['Commentaire']);?></textarea>
							</fieldset>
						</div>
						<div id="tabs-2">
							<h2>Reprise - Liste des hôtes et services</h2>
							<p>Cet onglet recence l'ensemble des hôtes et services rattachés à la prestation précédemment choisie.</p>
							<div id="accordionListe">
								<h3>Rechercher des hôtes</h3>
								<div id="recherche_hotes">
									<?php 
									include('recherche_hote.php');
									?>
								</div>
								<h3>Liste des hôtes</h3>
								<div id="liste_hote">
								<?php
									$monclient = htmlspecialchars($res_Demande['Code_Client']);
									include_once('requete_liste_hote_Reprise.php');
									?>
								</div>
								<h3>Liste des services</h3>
								<div id="liste_service">
									<?php
									$monclient = htmlspecialchars($res_Demande['Code_Client']);
									include_once('requete_liste_service_Reprise.php');
									?>
								</div>
								<h3>Liste des plages horaire</h3>
								<div id="liste_plage">
									<?php
									$monclient = htmlspecialchars($res_Demande['Code_Client']);
									include_once('requete_liste_plage_Reprise.php');
									?>
								</div>
							</div> 
							<button id="Valider_Selection" onclick="enregistre_selection()">Valider votre sélection</button><br />
						</div>
						<div id="tabs-3">
							<h2>Reprise - Paramétrage</h2>
							<div id="accordionParam">
								<?php
 									include_once('remplissage_param_global.php');
 								?>
<!-- 								<h3>Paramétrage des plages horaires</h3> -->
<!-- 								<div> -->
<!-- 									<div id="param_plage_horaire"> -->
										<?php
// 											include_once('remplissage_param_plage.php');
// 										?>
<!-- 									</div> -->
<!-- 									<span><button id="Ajouter_Plage">Ajouter une plage horaire</button></span> -->
<!-- 								</div> -->
<!-- 								<h3>Paramétrage des hôtes</h3> -->
<!-- 								<div> -->
<!-- 									<div id="param_hote"> -->
										<?php
// 											include_once('remplissage_param_hote.php');
// 										?>
<!-- 									</div> -->
<!-- 									<span><button id="Ajouter_Hote">Ajouter un hôte</button></span> -->
<!-- 								</div> -->
<!-- 								<h3>Paramétrage des services</h3> -->
<!-- 								<div> -->
<!-- 									<div id="param_service"> -->
										<?php
// 											include_once('remplissage_param_service.php');
// 										?>
<!-- 									</div> -->
<!-- 									<span><button id="Ajouter_Service">Ajouter un service</button></span> -->
<!-- 								</div> -->
							</div> 
								<span>
									<button id="Enregistrer_Brouillon" onclick="Enregistrer_Brouillon(true)">Enregistrer comme brouillon</button>
									<button id="Valider_Demande" onclick="Valider_Demande()">Valider votre demande</button>
								</span>
								<!-- <input type="submit" value="Valider votre demande." id="Valider_demande"/> -->
						</div>
					</div>
				<?php
				};
			};
			?>
			</section>
			<footer>
			<?php
			include_once('PiedDePage.php');
			?>
			</footer>
		</div>
		<!-- section des script javascript -->
		<?php
			include('section_script_JS.php');
		?>
	</body>
</html>
