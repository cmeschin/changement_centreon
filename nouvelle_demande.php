<!DOCTYPE html>
<?php
if (session_id()=='')
{
session_start();
};
?>

<html>
<head>
	<?php
		include('top.php');
		include('head.php');
	?>
</head>
<body>
<?php
	//include('top.php');
	include('log.php'); // chargement de la fonction de log
	addlog("chargement nouvelle demande.");

	// déclaration des constantes de la demande
	$date_demande=date("Y-m-d H:i:s");
	
	$date=date_create();
	date_add($date,date_interval_create_from_date_string('7 days'));
	$date_defaut=date_format($date,"Y-m-d");
	$ref_demande=date("ymdHi") . "-" . $_SESSION['user_changement_centreon'];
	$_SESSION['ref_dem'] = $ref_demande;
	$_SESSION['ID_dem'] = 0;
	addlog("Initialisation des constantes:");
	addlog("ID_demande:".$_SESSION['ID_dem']."");
	addlog("Date_demande:".$date_demande."");
	addlog("Ref_demande:".$ref_demande."");
	$_SESSION['Reprise'] = false; // Reprise demande
	$_SESSION['Nouveau'] = true; // Nouvelle demande
	$_SESSION['PDF'] = false;
	$R_ID_Demande=NULL;
	$_SESSION['R_ID_Demande'] =$R_ID_Demande; // Variable de session utilisée pour afficher directement une demande via le lien du ticket
?>	
<div id="principal">
	<header id="en-tete">
		<?php
			include('menu.php');
		?>
	</header>
	<section>
<!--	<fieldset class="liste_service_client">
		<legend>Liste des hôtes et services du client</legend> -->
<!--		<h2 class="demoHeaders">Accordion</h2> -->
		<div id="tabs">
			 <ul>
				<li id="lien_tabs1"><a href="#tabs-1">Info générales</a></li>
				<li id="lien_tabs2" style="visibility: hidden" onclick="Enregistrer_Brouillon(false)"><a href="#tabs-2">Liste hôtes et services</a></li>
				<li id="lien_tabs3" style="visibility: hidden"><a href="#tabs-3">Paramétrage</a></li>
			</ul>
			<div id="tabs-1">
				<h2>Informations générales sur la demande</h2>
				<fieldset id="info" class="info_generale">
					<span>
						<label for="demandeur">Demandeur :</label>
						<input readonly="readonly" type="text" id="demandeur" class="info_generale" name="demandeur" value="<?php echo $_SESSION['user_changement_centreon'] ;?>" size="15"/>
						
						<label for="date_demande">Date de la demande :</label>
						<input readonly="readonly" type="text" id="date_demande" class="info_generale" name="date_demande" value="<?php echo $date_demande ;?>" size="15"/>
						
						<label for="ref_demande">Référence de la demande :</label>
						<input readonly="readonly" type="text" id="ref_demande" class="info_generale" name="ref_demande" value="<?php echo $ref_demande ;?>" size="20"/>
						<label for="etat_demande">Etat :</label>
						<input readonly="readonly" type="text" id="etat_demande" class="info_generale" name="etat_demande" value="Brouillon" size="10"/>
					</span> <br />						
<!-- déplacé le 12/09/15 à côté de la prestation
						<label for="date_livraison_demandee" onclick="alert('Indiquez la date à laquelle vous souhaiteriez que la supervision soit en place, idéalement cela devrait être la date de démarrage en production.')" title="Cliquez pour plus d'informations.">Date de supervision souhaitée <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
						<input disabled="disabled" readonly="readonly" type="text" name="date_livraison_demandee" class="info_generale" id="date_livraison_demandee" value="" size="10" title="Cliquez sur le calendrier pour choisir la date."/>
						<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_date_livraison_demandee" /> <br />
 -->
					<label for="client" onclick="alert('Sélectionnez la prestation dans la liste; si elle n\'existe pas encore contactez l\'administrateur (05.57.22.77.13 ou centreon_tt@tessi.fr).')" title="Cliquez pour plus d'informations.">Prestation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
<!--						<select name="client" id="clientsup" class="info_generale" onChange="chargerlistes()" onblur="verifChamp(this)" title="Sélectionnez la prestation dans la liste ou choisissez nouveau si elle n'existe pas encore..."> -->  <!-- Liste Client -->
<!-- 						<select name="client" id="clientsup" class="info_generale" title="Sélectionnez la prestation dans la liste ou choisissez nouveau si elle n'existe pas encore..."> -->
 						<select name="client" id="clientsup" class="info_generale">
 							<option value="" selected="selected" >...</option> <!-- Valeur par défaut -->
<!-- Désactivé le 15/06/15 car trop de création exotiques -->
<!-- 							<option value="Nouveau">Nouveau</option> -->
							<?php
								include_once('connexion_sql_centreon.php'); 
								try {
									include_once('requete_liste_client.php');
								} catch (Exception $e) {
									echo '</select>';
									http_response_code(500);
									die('Erreur requete_liste_client: ' . $e->getMessage());
								};
								while ($res_client = $req_client->fetch())
								{ 
							?>
							<option value="<?php echo htmlspecialchars($res_client['Code_Client']) ?>"><?php echo htmlspecialchars($res_client['Code_Client']) ?></option> 
							<?php
								}; 
							?>
					</select>
					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_client" />

<!-- 					<span id="span_date_livraison_demandee" style="visibility: hidden;"> -->
						<label for="date_livraison_demandee" onclick="alert('Indiquez la date à laquelle vous souhaiteriez que la supervision soit en place, idéalement cela devrait être la date de démarrage en production.')" title="Cliquez pour plus d'informations.">Date de supervision souhaitée <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
						<input disabled="disabled" readonly="readonly" type="text" name="date_livraison_demandee" class="info_generale" id="date_livraison_demandee" value="<?php echo $date_defaut;?>" size="10" title="Cliquez sur le calendrier pour choisir la date."/>
						<img src="images/img_ok.png" class="verif" alt="correct" id="img_date_livraison_demandee" /> <br />
<!-- 					</span> -->					
<!-- Désactivé le 15/06/15 car trop de création exotique -->
<!-- 					<span id="sclient_new" style="visibility: hidden;"> -->
<!-- 						<input onblur="verifChamp(this)" type="text" name="client_new" id="client_new" class="info_generale" value="" placeholder="saisir le nom de la prestation..." size="50" maxlength="40" title="Saisissez le nom qui a été définit pour cette prestation lors du projet. Si vous ne le connaissez pas rapprochez vous du service qualité qui saura vous renseigner."/> -->
<!-- 						<img src="images/img_ok.png" class="verif" alt="correct" id="img_client_new" /> -->
<!-- 					</span> --> <br />
					<label for="email" onclick="alert('Saisissez ici les emails des personnes qui devront être notifiées de la demande, centreon_tt est automatiquement notifié de la demande.\nSéparez les adresses par un point-virgule.')" title="Cliquez pour plus d'informations.">Liste des personnes à notifier <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
					<input type="text" id="email" class="info_generale" name="email" value="<?php echo $_SESSION['email_changement_centreon'] ;?>" onblur="verifChampMail(this)" placeholder="séparez les adresses par un point-virgule" size="100"/>
					<?php if ($_SESSION['email_changement_centreon'] != "")
						{echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_email" />';
						} else
						{ echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_email" />';}?> <br/>
					<label for="commentaire" onclick="alert('Saisissez ici toute information complémentaire susceptible d\'être utile au paramétrage de la supervision')">Commentaires <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br/>
					<textarea id="commentaire" name="commentaire" class="info_generale" rows="5" cols="100"> </textarea>
<!--						<iimg src="images/img_ok.png" class="verif" alt="correct" id="img_commentaire" sstyle="visibility: hidden;"/> -->
				</fieldset>
<!--									<button class="Valider_Selection_info" id="Valider_Selection_info" onclick="enregistre_selection()">Valider votre sélection</button> -->
				<button class="info_suivant" id="info_suivant" onclick="onglet_suivant()">Suivant</button>
			</div>
			<div id="tabs-2">
				<h2>Liste des hôtes et services</h2>
				<p>Cet onglet recense l'ensemble des hôtes et services rattachés à la prestation précédemment choisie.</p>
				<div id="accordionListe">
					<h3>Rechercher des hôtes</h3>
					<div id="recherche_hotes">
						<?php 
						include('recherche_hote.php');
						?>
					</div>
					<h3>Liste des hôtes</h3>
					<div id="liste_hote"></div>
					<h3>Liste des services</h3>
					<div id="liste_service"></div>
					<h3>Liste des plages horaire</h3>
					<div id="liste_plage"></div>
				</div> 
				<button id="Valider_Selection" name="" onclick="enregistre_selection()">Valider votre sélection</button><br />
			</div>
			<div id="tabs-3">
				<div id="accordionParam">
					<h3>Paramétrage des plages horaires</h3>
					<div>
						<div id="param_plage_horaire">
						</div>
						<span><button id="Ajouter_Plage">Ajouter une plage horaire</button></span>
					</div>
					<h3>Paramétrage des hôtes</h3>
					<div>
						<div id="param_hote">
						</div>
						<span><button id="Ajouter_Hote">Ajouter un hôte</button></span>
					</div>
					<h3>Paramétrage des services</h3>
					<div>
						<div id="param_service">
						</div>
						<span><button id="Ajouter_Service">Ajouter un service</button></span>
					</div>
				</div> 
					<span>
						<button id="Enregistrer_Brouillon" onclick="Enregistrer_Brouillon(true)">Enregistrer comme brouillon</button>
						<button id="Valider_Demande" onclick="Valider_Demande()">Valider votre demande</button>
					</span><br/>
					<!-- <input type="submit" value="Valider votre demande." id="Valider_demande"/> -->
			</div>
		</div>

				<!-- Autocomplete -->
<!--				<h2 class="demoHeaders">Autocomplete</h2>
				<div>
					<input id="autocomplete" title="tapez votre recherche...">
				</div> -->
<!--		</fieldset> -->
	</section>
	<footer>
		<?php
			include('PiedDePage.php');
		?>
	</footer>
</div>
	<!-- section des script javascript -->
<?php
	include('section_script_JS.php');
?>

<script type="text/javascript">
$(function() {
// fonction de gestion de la liste des prestations
	 (function( $ ) {
		$.widget( "custom.combobox", {
			_create: function() {
				this.wrapper = $( "<span>" )
				.addClass( "custom-combobox" )
				.insertAfter( this.element );
				this.element.hide();
				this._createAutocomplete();
				this._createShowAllButton();
			},
			_createAutocomplete: function() {
					var selected = this.element.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
				this.input = $( "<input>" )
				.appendTo( this.wrapper )
				.val( value )
				.attr("size","45")
				.attr("onblur","verifChamp(this);chargerlistes()")
				.attr("id","client")
				.attr( "title", "" )
				.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
				.autocomplete({
					delay: 0,
					minLength: 0, // commence la recherche à X caractères
					source: $.proxy( this, "_source" )
					})
				.tooltip({
					tooltipClass: "ui-state-highlight"
				});
				this._on( this.input, {
				autocompleteselect: function( event, ui ) {
					ui.item.option.selected = true;
					this._trigger( "select", event, {
						item: ui.item.option
					});
				},
				autocompletechange: "_removeIfInvalid"
				});
			},
			_createShowAllButton: function() {
				var input = this.input,
				wasOpen = false;
				$( "<a>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Afficher tout" )
				.tooltip()
				.appendTo( this.wrapper )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "custom-combobox-toggle ui-corner-right" )
				.mousedown(function() {
					wasOpen = input.autocomplete( "widget" ).is( ":visible" );
				})
				.click(function() {
					input.focus();
					// Close if already visible
					if ( wasOpen ) {
						return;
					}
					// Pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
				});
			},
			_source: function( request, response ) {
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( this.element.children( "option" ).map(function() {
					var text = $( this ).text();
					if ( this.value && ( !request.term || matcher.test(text) ) )
					return {
						label: text,
						value: text,
						option: this
					};
				}) );
			},
			_removeIfInvalid: function( event, ui ) {
				// Selected an item, nothing to do
				if ( ui.item ) {
				return;
				}
				// Search for a match (case-insensitive)
				var value = this.input.val(),
				valueLowerCase = value.toLowerCase(),
				valid = false;
				this.element.children( "option" ).each(function() {
				if ( $( this ).text().toLowerCase() === valueLowerCase ) {
					this.selected = valid = true;
					return false;
				}
				});
				// Found a match, nothing to do
				if ( valid ) {
					return;
				}
				// Remove invalid value
				this.input
				.val( "" )
				.attr( "title", "[" + value + "] n'a aucune correspondance dans la liste..." )
				.tooltip( "open" );
				this.element.val( "" );
				this._delay(function() {
					this.input.tooltip( "close" ).attr( "title", "" );
					}, 2500 );
				this.input.data( "ui-autocomplete" ).term = "";
				$("#img_client").attr("alt","incorrect");
				$("#img_client").attr("src","images/img_ko.png");
			},
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			}
		});
	})( jQuery );
	$(function() {
	$( "#clientsup" ).combobox();
	//$( "#toggle" ).click(function() {
		//$( "#clientsup" ).toggle();
		//$( "#client" ).attr("style","visibility: hidden");
	//});
	});
});
</script>

</body>
</html>
