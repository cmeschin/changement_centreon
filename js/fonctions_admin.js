function charger_modele()
{
// charge les input correspondant au modèle sélectionné
var ma_selection= document.getElementById('Modele_Service').options[document.getElementById('Modele_Service').selectedIndex];
 	if(ma_selection.value =="Nouveau") {
		document.getElementById('Modele_Service_new').style.visibility = 'visible';
//		$("#argument_modele").empty(); // purge la liste à chaque nouvelle sélection
		var Nb_Lib = $("[name^='Libelle']").length;
		for (var j = Nb_Lib; j > 1;j--)
		{
			//alert("#Argument_Service"+j);
			$("#Argument_Service"+j).remove(); 
		};
		$("#Modele_Service_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau modele"
 		efface_Argument();

	} else if(ma_selection.value =="") {
		document.getElementById('Modele_Service_new').style.visibility = 'hidden';
		$("#Modele_Service_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau modele"
//		$("#argument_modele").empty(); // purge la liste à chaque nouvelle sélection
		var Nb_Lib = $("[name^='Libelle']").length;
		for (var j = Nb_Lib; j > 1;j--)
		{
			//alert("#Argument_Service"+j);
			$("#Argument_Service"+j).remove(); 
		};
	} else {
		document.getElementById('Modele_Service_new').style.visibility = 'visible';
		$("#Modele_Service_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau modele"
 		efface_Argument();
		//alert(ma_selection.innerHTML);
//		$("#argument_modele").empty(); // purge la liste à chaque nouvelle sélection
		var Nb_Lib = $("[name^='Libelle']").length;
		for (var j = Nb_Lib; j > 1;j--)
		{
			//alert("#Argument_Service"+j);
			$("#Argument_Service"+j).remove(); 
		};
		collecte_liste_argument(rempli_argument_admin);
		
	};
};

function ajoute_Argument() {
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			if (numtrou < h) // si le numargument à ajouter est inférieur au num argument max alors on insère dans le trou
			{ 
				$("#"+premierArgument).before(xhr.responseText);
			} else 
			{
				$("#"+premierArgument).after(xhr.responseText);
			};
			return xhr.responseText;
		};
	};
// compter le nombre total de fieldset
	var trou=false;
	var NbArgument = $("div.Argument_Modele_Service").length;
// boucler sur chaque fieldset pour identifier les trous
    queryAll = document.querySelectorAll('.Argument_Modele_Service');
	for (var i = 0; i< NbArgument; i++){
		var h = i+1;
		if (( "Argument_Service"+ h != queryAll[i].id) && (!trou)) {
	// Ajouter le fieldset dans le premier trou trouvé
			var numtrou = i;
			var Argumenttrou = "Argument_Service"+h;
			var premierArgument = queryAll[i].id; // on stocke le premier argument trouvé après le trou
			trou=true; 
			nouveau_Argument(numtrou);
			break;
		};
	
	}
	if (!trou){ // pas de trou dans la liste
		numtrou = NbArgument;
		premierArgument = "Argument_Service"+numtrou;
		nouveau_Argument(numtrou);
	}
	function nouveau_Argument(numtrou){
		numtrou = encodeURIComponent(numtrou);
		xhr.open("POST", "modele_Modele_Argument_Service.php", false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("NbArgument="+numtrou+"");
	}

};

function supprime_Argument(champ) {
	//alert("champ="+champ.innerHTML);
	var parent=$(champ).parent().attr("id");
	//alert("parent="+parent);
	var NbArgument = $("div.Argument_Modele_Service").length; // encodage inutile puisqu'il n'y a pas de transmission d'information à une autre page.
	if ( NbArgument == 1 ){
		var Message = "Il n'y a qu'un seul argument, vous ne pouvez pas le supprimer!";
		alert(Message);
	} else {
		var MessageConfirmation = "Vous allez supprimer l'" + parent + " . Etes-vous sûr?";
		if (confirm(MessageConfirmation)) {
			$("#"+parent).remove(); 
		};
	};
};

function efface_Argument() // efface tous les input texte Argument
{
	$("#Modele_Service_new input[type=text]").val('');
}; 

function enregistre_Modele()
{
	var Verif_Info = true;
//	$("#info > input[type=text],textarea,select").each(function()
	$("#Admin_Modele_Service .verif").each(function(){
		//alert($(this).attr("id"));
		if ($(this).attr("alt") != "correct"){
			//alert($(this).attr("id"));
			Verif_Info = false;
		};
	});
	if (Verif_Info != true) // Si tous les champs ne sont pas corrects
	{
		var message="STOP! Tous les champs ne sont pas valides!";
		alert(message);
		// retour en haut de la page...
		return false;
	} else
	{

		var Modele_Liste_Lib = new Array(); // peut également s'écrire: var liste_valeur = [];
		var Modele_Liste_Arg = new Array(); // peut également s'écrire: var liste_valeur = [];
		var Modele_Liste_Macro = new Array(); // peut également s'écrire: var liste_valeur = [];
		
		
	//Récuparation valeur Nom_Modele
	//alert($(":input#Modele_Service").val());
	//alert($("[name^='Modele_Service_new']").val());
	//alert($("#Modele_Service").value);
	//alert("ValeurSelect="+$(":input#Modele_Service").val())
		if ($(":input#Modele_Service").val() != "Nouveau")
		{
			var Nom_Modele_Service=$(":input#iModele_Service_new").val();
			var ID_Modele_Service=$(":input#Modele_Service").val();
			//alert("ID_Modele_Service"+ID_Modele_Service);
		} else
		{
			var ID_Modele_Service=0;
			var Nom_Modele_Service=$("[name^='iModele_Service_new']").val();
	//		alert("nouveau");
		};
		if ($(":checkbox#iModele_Service_Macro_new").is(':checked'))
		{
			var EST_MACRO=1;
		} else
		{
			var EST_MACRO=0;
		}
		
	//	alert("Nom_Modele_Service_Macro"+EST_MACRO);
		
		// stockage de la description
		var Modele_Service_Description=$(":input#Modele_Service_Description").val();
		
		var i=0;
		$("[name^='Libelle']").each(function()
		{ 
	//		if ($(this).val() && $(this).val() != "Nouveau")
	//		{
				Modele_Liste_Lib[i] = $(this).val();
				//alert("-"+Modele_Liste_Lib[i]+"-");
				i++;
	//		};
		});
		var i=0;
		$("[name^='Argument']").each(function()
		{ 
	//		if ($(this).val() && $(this).val() != "Nouveau")
	//		{
				Modele_Liste_Arg[i] = $(this).val();
				//alert("-"+Modele_Liste_Arg[i]+"-");
				i++;
	//		};
		});
		var i=0;
		$("[name^='Macro']").each(function()
		{ 
	//		if ($(this).val() && $(this).val() != "Nouveau")
	//		{
				Modele_Liste_Macro[i] = $(this).val();
				//alert("-"+Modele_Liste_Macro[i]+"-");
				i++;
	//		};
		});
	
	//	if (!liste_valeur[5]) // si le champ commentaire a été vidé on le rend non null.
	//	{
	//		liste_valeur[5]="";
	//	}
	//	if (liste_valeur.length <6) // l'onglet info générale doit contenir 6 valeurs impérativement
	//	{
	//		var message="STOP! Tous les champs de l'onglet Info Générales ne sont pas remplis!";
	//		alert(message);
	//		return False;
	//	}
	//	else
	//	{
	//		var Modele_Liste_Lib = "!" + Modele_Liste_Lib.join('!'); // convertis le tableau en une chaine séparée par un "!"
			var Modele_Liste_Lib = Modele_Liste_Lib.join('!'); // convertis le tableau en une chaine séparée par un "!"
	//		var Modele_Liste_Arg = "!" + Modele_Liste_Arg.join('!'); // convertis le tableau en une chaine séparée par un "!"
			var Modele_Liste_Arg = Modele_Liste_Arg.join('!'); // convertis le tableau en une chaine séparée par un "!"
			var Modele_Liste_Macro = Modele_Liste_Macro.join('!'); // convertis le tableau en une chaine séparée par un "!"
			//alert("Modele_Liste_Lib="+Modele_Liste_Lib);
			//alert("Modele_Liste_Arg="+Modele_Liste_Arg);
			//alert("Modele_Liste_Macro="+Modele_Liste_Macro);
	// concaténation des valeurs
			var Modele_valeur = ID_Modele_Service + "$" + Nom_Modele_Service + "$" + Modele_Service_Description + "$" + Modele_Liste_Lib + "$" + Modele_Liste_Arg + "$" + Modele_Liste_Macro + "$" + EST_MACRO;
	//	};
	
		
		function insertion_Modele(callback)
		{
			var xhr = getXMLHttpRequest(); //création de l'instance XHR
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
	//				collecte_liste_hote(rempli_hote); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
	//				collecte_liste_service(rempli_service); // fonction de remplissage de liste Param Service => fonction_remplissage.js
					callback(xhr.responseText); // C'est bon \o/
				} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
					alert('Une erreur est survenue !\n\nCode :' + xhr.status + '\nTexte : ' + xhr.responseText);
				};
			};
			var sModele_valeur = encodeURIComponent(Modele_valeur);
			//alert("Modele_valeur:"+sModele_valeur);
	//		alert("hote:"+shote_selec);
	//		alert("service:"+sservice_selec);
			
			xhr.open("POST", "insertion_Modele.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
			xhr.send("Modele_valeur="+sModele_valeur+""); 
			
		};
		function affiche_resultat_Modele(resultat){
			alert(resultat);
			var MessageConfirmation = "Voulez-vous recharger la page?";
			if (confirm(MessageConfirmation)) {
				window.location.reload();
			};
		};
		insertion_Modele(affiche_resultat_Modele);
	};
}; 

function collecte_liste_argument(callback) {
	var ma_selection= document.getElementById('Modele_Service').options[document.getElementById('Modele_Service').selectedIndex];
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText); // C'est bon \o/
		};
	};
//	var sID_Modele = encodeURIComponent(ma_selection.innerHTML);
	var sModele_Service = encodeURIComponent(ma_selection.innerHTML);
	//alert("sModele_Service"+sModele_Service);
	xhr.open("POST", "remplissage_admin_argument.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//	xhr.send("ID_modele="+sID_Modele+""); 
	xhr.send("Modele_Service="+sModele_Service+""); 
};

function rempli_argument_admin(liste_valeur)
{
//	Nb_Hote = resultat.substring(0,resultat.indexOf('$'));
//	Liste_Hote = resultat.substring(resultat.indexOf('$')+1);
//	alert("Nb_Hote="+Nb_Hote);
	//alert("liste_valeur="+liste_valeur);
// découpe de la chaine retournée
/*
	var Modele_Service = liste_valeur.substring(0,liste_valeur.indexOf('$')); // extraction Modele_Service
	//alert("Modele_Service="+Modele_Service);
	var tmp_Liste_Valeur = liste_valeur.substring(liste_valeur.indexOf('$')+1); // enlève Modele_Service de la liste
	//alert("tmp_Liste_Valeur1="+tmp_Liste_Valeur);
	var MS_Description = tmp_Liste_Valeur.substring(0,tmp_Liste_Valeur.indexOf('$')); // extraction MS_Description
	//alert("MS_Description="+MS_Description);
	var tmp_Liste_Valeur = tmp_Liste_Valeur.substring(tmp_Liste_Valeur.indexOf('$')+1); // enlève MS_Description de la liste
	//alert("tmp_Liste_Valeur2="+tmp_Liste_Valeur);
	var Liste_Libelles = tmp_Liste_Valeur.substring(0,tmp_Liste_Valeur.indexOf('$')); // extraction MS_Libelles
	var Liste_Arguments = tmp_Liste_Valeur.substring(tmp_Liste_Valeur.indexOf('$')+1); // extraction
*/
	var T_liste_valeur = liste_valeur.split("$");
//	découpage selon le modèle suivant:
//		0 => Modele_Service
//		1 => MS_Description
//		2 => MS_Libelles
//		3 => MS_Arguments
//		4 => MS_Macro
//		5 => MS_EST_MACRO
	var Modele_Service = T_liste_valeur[0]; // extraction Modele_Service
//	alert("Modele_Service="+Modele_Service);
	var MS_Description = T_liste_valeur[1]; // extraction MS_Description
//	alert("MS_Description="+MS_Description);
	var Liste_Libelles = T_liste_valeur[2]; // extraction MS_Libelles
//	alert("Liste_Libelle="+Liste_Libelles);
	var Liste_Arguments = T_liste_valeur[3]; // extraction MS_Arguments
//	alert("Liste_Arguments="+Liste_Arguments);
	var Liste_Macro = T_liste_valeur[4]; // extraction MS_Macro
//	alert("Liste_Macro="+Liste_Macro);
	var MS_EST_MACRO = T_liste_valeur[5]; // extraction MS_EST_MACRO
//	alert("MS_EST_MACRO="+MS_EST_MACRO);
	

	//alert("Liste_Libelles="+Liste_Libelles);
	//alert("Liste_Arguments="+Liste_Arguments);
//	var T_Valeur=liste_valeur.split("$"); // création du tableau principal
//	var NbLigne_T_Valeur = T_Valeur.length; // nb hote dans le tableau
	var Nb_Lib = $("[name^='Libelle']").length; // compte le nombre d'arguments
	var T_Libelles = Liste_Libelles.split("!");
	var T_Arguments = Liste_Arguments.split("!");
	var T_Macro = Liste_Macro.split("!");
	var Nb_LibArg = T_Libelles.length;
	var Nb_Arg = Nb_LibArg - Nb_Lib;
	//alert("Nb_Lib => "+ Nb_Lib);
	//alert("Nb_LibArg => "+ Nb_LibArg);
	//alert("Ajout nouveau argument Nb_Arg = "+Nb_LibArg+" - "+Nb_Lib+" => "+ Nb_Arg)
	
// boucle d'ajout des argument nécessaires	
	for (var i = 1;i <= Nb_Arg;i++)
	{
		ajoute_Argument();
	};

	//alert("Modele="+Modele_Service);
	$("#iModele_Service_new").val(Modele_Service);
	if (MS_EST_MACRO == 1)
	{
		$("#iModele_Service_Macro_new").attr('checked','checked');
	}else
	{
		$("#iModele_Service_Macro_new").removeAttr('checked');
	};
//	$("#iModele_Service_Macro_new").val(MS_EST_MACRO);
	$("#Modele_Service_Description").val(MS_Description);
// boucle sur chacun des arguments
	for (var i=0;i<Nb_LibArg;i++)
	{
//		var T_Valeur_Lig = T_Valeur[i].split("$"); // création tableau par ligne
		var j = i+1; // j correspond au numéro de fieldset hote
		//alert("#Libelle"+j+"="+T_Libelles[i]);
		//alert("#Argument"+j+"="+T_Arguments[i]);
		$("#Libelle"+j).val(T_Libelles[i]);
		$("#Argument"+j).val(T_Arguments[i]);
		$("#Macro"+j).val(T_Macro[i]);
		j++;
	};
};

function supprime_Modele()
{
	var ID_Modele_Service=$(":input#Modele_Service").val();
	var Nom_Modele_Service=$(":input#iModele_Service_new").val();
	var MessageConfirmation = "Vous allez supprimer le modèle de service [" + Nom_Modele_Service + "], ID[" + ID_Modele_Service + "] . Etes-vous sûr?";
		if (confirm(MessageConfirmation)) 
		{
				var xhr = getXMLHttpRequest(); //création de l'instance XHR
				xhr.onreadystatechange = function() 
				{
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						alert(xhr.responseText);
						window.location.reload();
					};
				};
				var sID_Modele_Service = encodeURIComponent(ID_Modele_Service);
				xhr.open("POST", "suppression_modele_service.php", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
				xhr.send("ID_Modele_Service="+sID_Modele_Service+""); 
		};
};

function charger_relation_modele()
{
// charge la liste déroulante des modèles associés correspondant à la selection
var ma_selection= document.getElementById('RM_Service').options[document.getElementById('RM_Service').selectedIndex];
	if(ma_selection.value =="") {
		//document.getElementById('Modele_Service_new').style.visibility = 'hidden';
		//$("#Modele_Service_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau modele"
		$("#Mod_Service_Associes select").val(""); // en cas de changement on vide la liste "modele associé"
//		$("#argument_modele").empty(); // purge la liste à chaque nouvelle sélection
//		var Nb_Lib = $("[name^='Libelle']").length;
//		for (var j = Nb_Lib; j > 1;j--)
//		{
//			//alert("#Argument_Service"+j);
//			$("#Argument_Service"+j).remove(); 
//		};
	} else {
		//document.getElementById('RM_Service').style.visibility = 'visible';
		//$("#Modele_Service_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau modele"
		function charge_liste_associe(callback) 
		{
			//var ma_selection= document.getElementById('Modele_Service').options[document.getElementById('Modele_Service').selectedIndex];
			var xhr = getXMLHttpRequest(); //création de l'instance XHR
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
					callback(xhr.responseText); // C'est bon \o/
				};
			};
		//	var sID_Modele = encodeURIComponent(ma_selection.innerHTML);
			var sModele_Service = encodeURIComponent(ma_selection.innerHTML);
			//alert("sModele_Service"+sModele_Service);
			xhr.open("POST", "charge_liste_modele_associe.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
			xhr.send("Modele_Service="+sModele_Service+""); 
		};

		function rempli_liste_associes(liste_associes)
		{
			//alert(liste_associes);
			$("#Liste_Associe").empty(); // purge la liste à chaque nouvelle sélection d'hôte
			$("#Liste_Associe").append(liste_associes); // rempli la liste avec la sélection courante
			//$("#Liste_Associe").prepend(liste_associes); // rempli la liste avec la sélection courante
			//document.getElementById('Mod_Service_Associes').style.visibility = 'visible';
			//document.getElementById('Dissocier_Selection').style.visibility = 'visible';
		};
		function charge_liste_centreon(callback) 
		{
			//var ma_selection= document.getElementById('Modele_Service').options[document.getElementById('Modele_Service').selectedIndex];
			var xhr = getXMLHttpRequest(); //création de l'instance XHR
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
					callback(xhr.responseText); // C'est bon \o/
				};
			};
		//	var sID_Modele = encodeURIComponent(ma_selection.innerHTML);
			var sModele_Service = encodeURIComponent(ma_selection.innerHTML);
			//alert("sModele_Service"+sModele_Service);
			xhr.open("POST", "charge_liste_modele_Centreon.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		//	xhr.send("ID_modele="+sID_Modele+""); 
			xhr.send("Modele_Service="+sModele_Service+""); 
		};

		function rempli_liste_centreon(liste_centreon)
		{
			//alert(liste_centreon);
			$("#Liste_Centreon").empty(); // purge la liste à chaque nouvelle sélection d'hôte
			$("#Liste_Centreon").append(liste_centreon); // rempli la liste avec la sélection courante
			//$("#Liste_Centreon").prepend(liste_centreon); // rempli la liste avec la sélection courante
			//document.getElementById('Mod_Service_Centreon').style.visibility = 'visible';
			//document.getElementById('Associer_Selection').style.visibility = 'visible';
		};
		charge_liste_associe(rempli_liste_associes);
		charge_liste_centreon(rempli_liste_centreon);

	};
};

function Associer_Selection()
{
	var Liste_Centreon = '';
// enregistrer la sélection	
	$("select[id='Mod_Service_Centreon'] option:selected").each(function() {
		Liste_Centreon += $(this).val() + '|' ;
	});
	Liste_Centreon = Liste_Centreon.substring(0,Liste_Centreon.length-1); // Supprime le dernier caractère de la chaine
	//alert("Liste_Centreon="+Liste_Centreon);
   var Modele_Service = $("select[id='RM_Service'] option:selected").val(); // retourne la valeur du modèle sélectionné
   //alert("Modele_Service="+Modele_Service);
   var Liste_MS = Modele_Service + '$' + Liste_Centreon;
   //alert("Liste_MS="+Liste_MS);
// lance la requête via PHP
   var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			//alert("rechargement");
			charger_relation_modele();
		};
	};
	var sListe_MS = Liste_MS;
	//alert("sListe_MS="+sListe_MS);
	xhr.open("POST", "modele_service_associes_Ajout.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("Liste_MS="+sListe_MS+"");
};

function Dissocier_Selection()
{
	//alert("Dissocier sélection à faire!!!");
	var Liste_Associes = '';
// enregistrer la sélection	
	$("select[id='Mod_Service_Associes'] option:selected").each(function() {
		Liste_Associes += $(this).val() + '|' ;
	});
	Liste_Associes = Liste_Associes.substring(0,Liste_Associes.length-1); // Supprime le dernier caractère de la chaine
	//alert("Liste_Centreon="+Liste_Centreon);
   var Modele_Service = $("select[id='RM_Service'] option:selected").val(); // retourne la valeur du modèle sélectionné
   //alert("Modele_Service="+Modele_Service);
   var Liste_MS = Modele_Service + '$' + Liste_Associes;
   //alert("Liste_MS="+Liste_MS);
// lance la requête via PHP
   var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			//alert("rechargement");
			charger_relation_modele();
		};
	};
	var sListe_MS = Liste_MS;
	//alert("sListe_MS="+sListe_MS);
	xhr.open("POST", "modele_service_associes_Suppr.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("Liste_MS="+sListe_MS+"");
	
};

function MAJ_Modele_Centreon(callback)
{
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	var loading=false;
	xhr.onreadystatechange = function() 
	{
//		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 

//		{
//			alert("MAJ effectuée");
//			//window.location.reload();
//		};
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			$("#img_loading").remove();
			$("#p_loading").remove();
			//callback(xhr.responseText); // C'est bon \o/
			alert("MAJ effectuée");
			//window.location.reload();
		} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
			$("#img_loading").remove();
			$("#p_loading").remove();
//			$("#"+hote_bouton_id+"").removeAttr("Disabled"); // réactive le bouton
			gestion_erreur(xhr);
		} else if (loading == false){
			loading=true;
			$("fieldset#Admin_Modele_Centreon").prepend('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant la mise à jour des informations..."/> ');
			$("fieldset#Admin_Modele_Centreon").prepend('<p id="p_loading">Veuillez patienter pendant la mise à jour des informations...</p>');
		};

	};
	
	xhr.open("POST", "MAJ_modele_Centreon.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send(); 
};


function Associer_Selection_am()
{
	var am_select = '';
// enregistrer la sélection	
	$("select[id='am_dispo'] option:selected").each(function() {
		am_select = $("#am_dispo option:selected").val();
		//alert("am_select="+am_select);
		$("#am_associe").append('<option value="'+ am_select +'">'+ am_select.substring(am_select.indexOf("_")+1) + '</option>'); // on ajoute à la liste déroulante am_associe l'am sélectionnée
		$("#am_dispo option[value='" + am_select + "']").remove(); // on supprime de la liste déroulante l'am sélectionnée
	});
};

function Dissocier_Selection_am()
{
	//alert("Dissocier sélection à faire!!!");
	var am_select = '';
// enregistrer la sélection	
	$("select[id='am_associe'] option:selected").each(function() {
		am_select = $("select[id='am_associe'] option:selected").val();
		$("#am_dispo").append('<option value="'+ am_select +'">'+ am_select.substring(am_select.indexOf("_")+1) + '</option>'); // on ajoute à la liste déroulante am_associe l'am sélectionnée
		$("#am_associe option[value='" + am_select + "']").remove(); // on supprime de la liste déroulante l'am sélectionnée
	});
};

//function config_notification()
//{
//	var xhr = getXMLHttpRequest(); //création de l'instance XHR
//	var loading=false;
//	xhr.onreadystatechange = function() 
//	{
//		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
//			$("#img_loading").remove();
//			$("#p_loading").remove();
////				callback(xhr.responseText); // C'est bon \o/
//			$("#field_config_notification").append(xhr.responseText);
////				//alert("MAJ effectuée");
////				//window.location.reload();
//		} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
//			$("#img_loading").remove();
//			$("#p_loading").remove();
////				$("#"+hote_bouton_id+"").removeAttr("Disabled"); // réactive le bouton
//			gestion_erreur(xhr);
//		} else if (loading == false){
//			loading=true;
//			$("fieldset#field_config_notification").prepend('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant le chargement du formulaire..."/> ');
//			$("fieldset#field_config_notification").prepend('<p id="p_loading">Veuillez patienter pendant le chargement du formulaire...</p>');
//		};
//	};
//	
//	xhr.open("POST", "BAM_config_notifications.php", true);
//	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//	xhr.send(); 
//};

function Envoie_Mail_BAM()
{
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			alert("Mail envoyé");
			//window.location.reload();
		};
	};
	xhr.open("POST", "automate_mail.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send(); 
};