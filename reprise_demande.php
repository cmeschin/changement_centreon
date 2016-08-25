<!DOCTYPE html>
<?php
if (session_id()=='')
{
	session_start();
};
$ID_Demande = (isset($_GET["id_demande"])) ? $_GET["id_demande"] : NULL;
$Demandeur = (isset($_GET["demandeur"])) ? $_GET["demandeur"] : NULL;
$_SESSION['Nouveau'] = false; // Il ne s'agit pas d'une nouvelle demande
$_SESSION['Reprise'] = true; // Il s'agit d'une reprise
$_SESSION['Extraction'] = false; //Il ne s'agit pas d'une extraction
$_SESSION['PDF'] = false; // Il ne s'agit pas d'une extraction PDF
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
				echo '<h3>Bonjour, ' . $_SESSION['name_changement_centreon'] . ' vous n\'êtes pas autorisé à éditer cette demande car vous n\'êtes ni administrateur ni l\'auteur de celle-ci!</h3>';
				$aujourdhui=date("d-m-Y H:i:s");
				echo '<p>nous sommes le ' . $aujourdhui . '</p>';
				echo '<p>Merci de choisir un menu ci dessus!</p>';
			} else // user= demandeur ou administrateur
			{
				// récupération des informations liées à la demande
				include_once('connexion_sql_supervision.php');
				try 
				{
					include_once('requete_reprise_demande.php');
				} catch (Exception $e) 
				{
					die('Erreur requete_reprise_demande: ' . $e->getMessage());
				};
				$res_SelectDemande = $Select_Demande->fetchAll();
				if ($res_SelectDemande == False)
				{
					echo '<p class="attention">Cette demande n\'existe plus, elle a été supprimée.</p>';
					echo '<p class="attention">Veuillez consulter la liste des demandes en cours ou contacter l\'administrateur.</p>';
				}else 
				{
					foreach($res_SelectDemande as $res_Demande)
					{
						$date_demande = htmlspecialchars($res_Demande['Date_Demande']);
						$ref_demande = htmlspecialchars($res_Demande['Ref_Demande']);
						$etat_demande = htmlspecialchars($res_Demande['Etat_Demande']);
						If ($etat_demande != "Brouillon")
						{
							echo '<p class="attention">La demande ' . $ref_demande . ' du ' . $date_demande . ' n\'est plus un brouillon (statut actuel: ' . $etat_demande . '), vous ne pouvez plus travailler dessus.</p>';
							echo '<p class="attention">Veuillez vérifier votre requête ou contacter l\'administrateur.</p>';
							addlog("Ref_demande erronée:".$ref_demande."");
						} else
						{
							// déclaration des constantes de la demande
							$date_livraison = htmlspecialchars($res_Demande['Date_Supervision_Demandee']);
							if ($date_livraison < date("Y-m-d"))
							{
								$nouvelle_date=date_create();
								date_add($nouvelle_date,date_interval_create_from_date_string('7 days'));
								$date_livraison=date_format($nouvelle_date,"Y-m-d");
							}
							$_SESSION['ref_dem'] = htmlspecialchars($res_Demande['Ref_Demande']);
							$_SESSION['ID_dem'] = htmlspecialchars($res_Demande['ID_Demande']);
							$_SESSION['Code_Client'] = htmlspecialchars($res_Demande['Code_Client']);
							addlog("Initialisation des constantes:");
							addlog("Date_demande:".$date_demande."");
							addlog("Ref_demande:".$ref_demande."");
							addlog("Prestation:".$res_Demande['Code_Client']."");
							
							?>	
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
										</span> <br />									
										<span>
											<label for="type_demande">Etat :</label>
		<!-- 								<input readonly="readonly" type="text" id="type_demande" class="info_generale" name="type_demande" value="<?php echo htmlspecialchars($res_Demande['Type_Demande']);?>" size="10"/> -->
											<?php 
											echo '<select Disabled="Disabled" name="type_demande" id="type_demande" class="info_generale" onblur="verifChamp(this)">';
											if (htmlspecialchars($res_Demande['Type_Demande']) == "Demarrage")
											{
												echo '<option value="MiseAJour">Mise à jour</option>';
												echo '<option Selected="Selected" value="Demarrage">Démarrage en production</option>';
											} else 
											{
												echo '<option Selected="Selected" value="MiseAJour">Mise à jour</option>';
												echo '<option value="Demarrage">Démarrage en production</option>';
											};
					 						echo '</select>';
					 						?>
											<img src="images/img_ok.png" class="verif" alt="correct" id="img_type_demande" ondblclick="deverouille_liste(this)" />
											<label for="date_livraison_demandee" onclick="alert('Indiquez la date à laquelle vous souhaiteriez que la supervision soit en place, idéalement la date de démarrage en production.\nCliquez sur le calendrier pour choisir une date.')" title="Cliquez pour plus d'informations.">Date de supervision souhaitée <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
											<input readonly="readonly" type="text" name="date_livraison_demandee" class="info_generale" id="date_livraison_demandee" onblur="verifChamp(this)" value="<?php echo $date_livraison;?>" size="10"/>
											<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_date_livraison_demandee" />
										</span> <br />									
											
										<label for="client" onclick="alert('Une fois que la demande est initialisée, il n\'est plus possible de changer la prestation.')" title="Cliquez pour plus d'informations.">Prestation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
										<select Disabled="Disabled" name="client" id="clientsup" class="info_generale">  <!-- Liste Client -->
										<?php
											echo '<option Selected="Selected" value="' . htmlspecialchars($res_Demande['Code_Client']) . '">' . htmlspecialchars($res_Demande['Code_Client']) . '</option>';
										?>
										</select>
										<img src="images/img_ver.png" class="verif" alt="correct" id="img_client" /> <br />
										<label for="email" onclick="alert('Saisissez ici les emails des personnes qui devront être notifiées de la demande, centreon_tt est automatiquement notifié de la demande.\nSéparez les adresses par un point-virgule.')" title="Cliquez pour plus d'informations.">Liste des personnes à notifier <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
										<input type="text" id="email" class="info_generale" name="email" value="<?php echo htmlspecialchars($res_Demande['email']) ;?>" onblur="verifChampMail(this)" placeholder="séparer les adresses par un point-virgule" size="100"/>
										<img src="images/img_ok.png" class="verif" alt="correct" id="img_email" /> <br/>
					
										<label for="commentaire" onclick="alert('Saisissez ici toute information complémentaire susceptible d\'être utile au paramétrage de la supervision')" title="Cliquez pour plus d'informations.">Commentaires <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br/>
										<textarea id="commentaire" name="commentaire" class="info_generale" rows="5" cols="100"><?php echo htmlspecialchars($res_Demande['Commentaire']);?></textarea>
									</fieldset>
								</div>
								<div id="tabs-2">
									<h2>Reprise - Liste des hôtes et services</h2>
									<p>Cet onglet recense l'ensemble des hôtes et services rattachés à la prestation précédemment choisie.</p>
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
									</div> 
										<span>
											<div id="bip"></div>
											<button id="Enregistrer_Brouillon" onclick="Enregistrer_Brouillon(true)">Enregistrer comme brouillon</button>
											<button id="Valider_Demande" onclick="Valider_Demande()">Valider votre demande</button>
										</span>
										<!-- <input type="submit" value="Valider votre demande." id="Valider_demande"/> -->
								</div>
							</div>
						<?php
						};
					};
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
