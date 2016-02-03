<!DOCTYPE html>
<html>
<head>
<?php
/*	if (session_id()=='')
	{
		session_start();
	};*/
	include ('top.php');
	include ('head.php');
?>
</head>
<body>
	<div id="principal">
		<header id="en-tete">
	<?php
	include ('menu.php');
	$_SESSION['ref_tmp_extract'] = $_SESSION['user_changement_centreon'] . "_" . date( "ymdHis" );
	$_SESSION['R_ID_Demande'] = "extraction"; // astuce pour gérer le verroullage des champs arguments de service sur une extraction
	$_SESSION['Reprise'] = false;
	$_SESSION['Nouveau'] = false;
	$_SESSION['PDF'] = false;
	
	?>
	</header>
		<section>
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Extraction</a></li>
				</ul>
				<div id="tabs-1">
					<h2>Extraire une prestation</h2>
					<fieldset id="extraire" class="extraire_prestation">
					<?php
					echo '<label for="prestation" onclick="alert(\'Sélectionnez la prestation dont vous souhaitez faire une extraction\')">Prestation <img alt="point_interrogation" src="images/point-interrogation-16.png">:</label>';
					// echo '<select name="prestation" id="prestation" class="extraire_prestation" onblur="verifChamp(this)">';
					echo '<select name="prestation" id="prestation" class="extraire_prestation">';
					echo '<option value="" selected="selected" >...</option>';
					include_once ('connexion_sql_centreon.php');
					include_once ('requete_liste_client.php');
					
					while ( $res_client = $req_client->fetch () )
					{
						if (htmlspecialchars($res_client['Actif']) == '1')
						{
							echo '<option value="' . htmlspecialchars ( $res_client ['Code_Client'] ) . '">' . htmlspecialchars ( $res_client ['Code_Client'] ) . '</option>';
						};
					};
					echo '</select>';
					echo '<button id="valider_extraction" onclick="valider_extraction()">Extraire cette prestation</button>';
					echo '<button id="valider_extraction_pdf" onclick="valider_extraction_pdf()">Extraire cette prestation en PDF</button>';
					?>
					</fieldset>
					<!--  <fieldset style="visibility: hidden" id="extraction_elements" class="extraire_prestation"> -->
					<fieldset id="extraction_elements" class="extraire_prestation">
					</fieldset>
				</div>
			</div>
		</section>
		<footer>
		<?php
		include ('PiedDePage.php');
		?>
	</footer>
	</div>
<?php
include ('section_script_JS.php');
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
				//.attr("onblur","verifChamp(this);chargerlistes()")
				//.attr("onChange","chargerlistes()")
				.attr("id","prestation")
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
				.attr( "title", "["+value + "] n'a aucune correspondance dans la liste" )
				.tooltip( "open" );
				this.element.val( "" );
				this._delay(function() {
					this.input.tooltip( "close" ).attr( "title", "" );
					}, 2500 );
				this.input.data( "ui-autocomplete" ).term = "";
				//$("#img_client").attr("alt","incorrect");
				//$("#img_client").attr("src","images/img_ko.png");
			},
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			}
		});
	})( jQuery );
	$(function() {
	$( "#prestation" ).combobox();
	//$( "#toggle" ).click(function() {
		//$( "#clientsup" ).toggle();
		//$( "#client" ).attr("style","visibility: hidden");
	//});
	});
});
</script>

</body>
</html>
