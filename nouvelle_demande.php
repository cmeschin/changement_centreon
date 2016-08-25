<!DOCTYPE html>
<?php
if (session_id()=='')
{
session_start();
};

echo '<html>';
echo '<head>';
include('top.php');
include('head.php');

echo '</head>';
echo '<body>';
include('log.php'); // chargement de la fonction de log
include_once('connexion_sql_centreon.php');
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
$_SESSION['Reprise'] = false; // Il ne s'agit pas d'une reprise demande
$_SESSION['Nouveau'] = true; // Il s'agit d'une nouvelle demande
$_SESSION['Extraction'] = false; // Il ne s'agit pas d'une extraction PDF
$_SESSION['PDF'] = false; // Il ne s'agit pas d'une extraction PDF
$_SESSION['Recherche'] = false; // Il ne s'agit pas d'une recherche

?>	
<div id="principal">
	<header id="en-tete">
		<?php
			include('menu.php');
		?>
	</header>
	<section>
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
					</span> <br />
					<span>
						<label for="type_demande">Type de la demande :</label>
 						<select name="type_demande" id="type_demande" class="info_generale" onblur="verifChamp(this)">
 							<option value="" selected="selected">...</option> <!-- Valeur par défaut -->
 							<option value="MiseAJour">Mise à jour</option>
 							<option value="Demarrage">Démarrage en production</option>
 						</select>
						<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_type_demande" />
						<label for="date_livraison_demandee" onclick="alert('Indiquez la date à laquelle vous souhaiteriez que la supervision soit en place, idéalement cela devrait être la date de démarrage en production.\nCliquez sur le calendrier pour choisir une date.')" title="Cliquez pour plus d'informations.">Date de supervision souhaitée <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
						<input readonly="readonly" type="text" name="date_livraison_demandee" class="info_generale" id="date_livraison_demandee" onblur="verifChamp(this)" value="<?php echo $date_defaut;?>" size="10"/>
						<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_date_livraison_demandee" />
					</span> <br />						
					<label for="client" onclick="alert('Sélectionnez la prestation dans la liste; si elle n\'existe pas encore contactez l\'administrateur (05.57.22.77.13 ou centreon_tt@tessi.fr).')" title="Cliquez pour plus d'informations.">Prestation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
 						<select name="client" id="clientsup" class="info_generale">
 							<option value="" selected="selected" >...</option> <!-- Valeur par défaut -->
							<?php
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
					<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_client" /> <br />
					<label for="email" onclick="alert('Saisissez ici les emails des personnes qui devront être notifiées de la demande, centreon_tt est automatiquement notifié de la demande.\nSéparez les adresses par un point-virgule.')" title="Cliquez pour plus d'informations.">Liste des personnes à notifier <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>
					<input type="text" id="email" class="info_generale" name="email" value="<?php echo $_SESSION['email_changement_centreon'] ;?>" onblur="verifChampMail(this)" placeholder="séparez les adresses par un point-virgule" size="100"/>
					<?php if ($_SESSION['email_changement_centreon'] != "")
						{echo '<img src="images/img_ok.png" class="verif" alt="correct" id="img_email" />';
						} else
						{ echo '<img src="images/img_edit.png" class="verif" alt="incorrect" id="img_email" />';}?> <br/>
					<label for="commentaire" onclick="alert('Saisissez ici toute information complémentaire susceptible d\'être utile au paramétrage de la supervision')">Commentaires <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label> <br/>
					<textarea id="commentaire" name="commentaire" class="info_generale" rows="5" cols="100"> </textarea>
				</fieldset>
				<button disabled="Disabled" class="info_suivant" id="info_suivant" onclick="onglet_suivant()">Suivant</button>
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
				<div id="messageValidation"></div>
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
						<div id="bip"></div>
						<button id="Enregistrer_Brouillon" onclick="Enregistrer_Brouillon(true)">Enregistrer comme brouillon</button>
						<button id="Valider_Demande" onclick="Valider_Demande()">Valider votre demande</button>
					</span><br/>
			</div>
		</div>
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
				$("#liste_hote").empty(); // purge la liste à chaque nouvelle sélection de prestation
		        $("#liste_hote").append('<table id="T_Liste_Hote"><tr><th>Sélection</th><th>Hôte</th><th>Description</th><th>Adresse IP</th><th>Controle</th><th hidden>host_id</th></tr></table>');// Hôte
				$("#liste_service").empty(); // purge la liste à chaque nouvelle sélection de prestation
		        $("#liste_service").append('<table id="T_Liste_Service"><tr><th>Selection</th><th>Hôte</th><th>Service</th><th>Fréquence</th><th>Plage Horaire</th><th>Controle</th></tr></table>');
		        $("#liste_plage").empty(); // purge la liste à chaque nouvelle sélection de prestation
		        $("#liste_plage").append('<table id="T_Liste_Plage"><tr><th>Sélection</th><th>Plage Horaire</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th></tr></table>');
				$("#img_client").attr("alt","incorrect");
				$("#img_client").attr("src","images/img_ko.png");
				$("#p_loading").remove();
				$("#img_loading").remove();
			},
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			}
		});
	})( jQuery );
	$(function() {
	$( "#clientsup" ).combobox();
	});
});
</script>

</body>
</html>
