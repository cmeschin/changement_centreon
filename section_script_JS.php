<script src="js/jquery-1.10.2.js" type="text/javascript"></script>
<!-- <script src="js/jquery-ui-1.10.4.custom.js" type="text/javascript"></script> -->
<script src="js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<!-- <script src="js/development-bundle/ui/i18n/jquery.ui.datepicker-fr.js" type="text/javascript"></script> -->
<script src="js/jquery.ui.datepicker-fr.min.js" type="text/javascript"></script>
<script src="js/fonctions_admin.js" type="text/javascript"></script>
<script src="js/fonctions_enregistrer.js" type="text/javascript"></script>
<script src="js/fonctions_liste.js" type="text/javascript"></script>
<script src="js/fonctions_remplissage.js" type="text/javascript"></script>
<script src="js/fonctions_remplissage_DEC.js" type="text/javascript"></script>
<script src="js/verif_champ.js" type="text/javascript"></script>
<script src="js/oXHR.js" type="text/javascript"></script>
<script src="js/jquery.weekLine.min.js"></script> 
<!-- <script src="js/jquery.timepicker.min.js"></script> -->
<script src="js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
$(function() {
	$( document ).tooltip();
	$("#accordionListe").accordion({ heightStyle: "content", collapsible: true, active: 2 }); // affiche l'onglet Liste Service par défaut
	$("#accordionParam").accordion({ heightStyle: "content", collapsible: true});
//	$("#accordionDoc").accordion({ heightStyle: "content", collapsible: true, active: false });
	$("#accordionDoc_gen").accordion({ heightStyle: "content", collapsible: true, active: false});
	$("#accordionDoc_nouveau").accordion({ heightStyle: "content", collapsible: true, active: false});
	$("#accordionDoc_reprise").accordion({ heightStyle: "content", collapsible: true, active: false});
	$("#accordionDoc_liste").accordion({ heightStyle: "content", collapsible: true, active: false});
	$("#accordionDoc_truc").accordion({ heightStyle: "content", collapsible: true, active: false});	
//		$( "#accordionPlage" ).accordion({ heightStyle: "content" });
	$("#accordion_bam_notifications").accordion({ heightStyle: "content", collapsible: true, active: 0});	
	/** Days to be disabled as an array */
	var disabledSpecificDays = ["1-1-2016", "9-15-2015", "9-17-2015"];
	//var disabledSpecificDays = [""];
	function disableSpecificDaysAndWeekends(date) {
		var m = date.getMonth();
		var d = date.getDate();
		var y = date.getFullYear();
		for (var i = 0; i < disabledSpecificDays.length; i++) {
			if ($.inArray((m + 1) + '-' + d + '-' + y, disabledSpecificDays) != -1 || new Date() > date) {
				return [false];
			};
		};
		var noWeekend = $.datepicker.noWeekends(date);
		return !noWeekend[0] ? noWeekend : [true];
	};
	$("#date_livraison_demandee").datepicker({
		showOn: "button",
		dateFormat: "yy-mm-dd",
		buttonImage: "images/calendar.gif",
		buttonImageOnly: true,
		buttonText: "Choix de la date",
		//beforeShowDay: disableSpecificDaysAndWeekends, // blocage des WE
		beforeShowDay: function(date){ return [date.getDay() != 6 && date.getDay() != 0,""]},
		minDate: 0, //blocage de saisie d'une date antérieur à J
		onSelect: function(dateStr) {var date_test= $(this).datepicker('getDate');verifChampDate_date_livraison_demandee(this,date_test);}
	});
	$("#tabs").tabs();
	$("#tabs_DEC").tabs();
	$("#tabs_Doc").tabs();
	$("#Ajouter_Argument").click(function(){
		ajoute_Argument();
	});
	$("#Ajouter_Hote").click(function(){
		ajoute_fieldset_hote();
	});
	$("#Ajouter_Plage").click(function(){
		ajoute_fieldset_plage();
	});
	$("#Ajouter_Service").click(function(){
		ajoute_fieldset_service();
	});
	$("#MAJ_Modele_Centreon").click(function(){
		MAJ_Modele_Centreon();
	});
	$("#Supprimer_Modele").click(function(){
		supprime_Modele();
	});
 	$('#gb_heure').datetimepicker({
 		datepicker:false,
 		format:'H:i',
 		step: 30,
 		validateOnBlur:true
 	});
});
</script>