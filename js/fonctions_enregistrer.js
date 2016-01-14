function onglet_suivant()
{
	var Verif_Info = true;
	$("#info .verif").each(function(){
		if ($(this).attr("alt") != "correct"){
			Verif_Info = false;
		};
	});

	if (Verif_Info != true) // l'onglet info générale doit contenir 7 valeurs impérativement
	{
		var message="STOP! Tous les champs de l'onglet Info Générales ne sont pas valides!";
		alert(message);
		$("#Rechercher").removeAttr("Disabled");
		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
		$("#Valider_Selection").removeAttr("Disabled");
		$("#lien_tabs2").attr("style","visibility: hidden");
		$("#lien_tabs3").attr("style","visibility: hidden");
		// retour en haut de la page...
		$('html,body').animate({scrollTop:0}, 'slow'); // retourne en haut de la page
		return false;
	} else
	{
		$("#lien_tabs2").removeAttr("style");
		$("#lien_tabs2").effect("pulsate");
		// retour en haut de la page...
		$('html,body').animate({scrollTop:0}, 'slow'); // retourne en haut de la page

	};
};

function enregistre_selection()
{
	/**
	 * Fonction d'enregistrement du tout premier brouillon avec la sélection actuelle (Bouton "Valider votre sélection")
	 */
// désactivation du bouton "Valider Votre Sélection" pour ne pas cliquer deux fois dessus
	$("#Rechercher").attr("Disabled","Disabled");
	$("#Ajouter_Selection_Hote").attr("Disabled","Disabled");
	$("#Valider_Selection").attr("Disabled","Disabled");
/////////////////////////////////////////
// Traitement de l'onglet Info Généralres
/////////////////////////////////////////
//	var liste_valeur = new Array(); // peut également s'écrire: var liste_valeur = [];
//	var i=0;
	var Verif_Info = true;
//	$("#info > input[type=text],textarea,select").each(function()
	$("#info .verif").each(function(){
		//alert($(this).attr("alt"));
		if ($(this).attr("alt") != "correct"){
			Verif_Info = false;
		};
	});

	if (Verif_Info != true) // l'onglet info générale doit contenir 7 valeurs impérativement
	{
		var message="STOP! Tous les champs de l'onglet Info Générales ne sont pas valides!";
		alert(message);
		$("#Rechercher").removeAttr("Disabled");
		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
		$("#Valider_Selection").removeAttr("Disabled");
		//$("#lien_tabs2").attr("style","visibility: hidden");
		//$("#lien_tabs3").attr("style","visibility: hidden");
		return false;
	};
//	} else
//	{
//		$("#lien_tabs3").removeAttr("style");
//	};

	//appel de la fonction de récupération des infos générales
	var info_gen = info_generale();
	
/////////////////////////////////////////////////////
// Traitement de l'onglet Liste des hôtes et services
/////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////
	// boucler sur les hôtes sélectionnés afin de générer une liste
////////////////////////////////////////////////////////////////////

	//var tableau_hote = new Array(); // équivalent de var tableau_hote = new array();
//	var hote_selec = new Array();
	var hote_liste = new Array();
	//tableau_hote = document.getElementById("T_Liste_Hote"); // Initialise le tableau des hôtes
	var tableau_hote_lig = document.getElementById("T_Liste_Hote").rows; //charge les lignes du tableau
	var NbLigne_Hote = tableau_hote_lig.length; // récupération du nombre d'enregistrement
//	var nbcheck_hote = $('input:checked[name=selection_hote]').size(); // récupère le nombre d'hôte sélectionné
	
	//alert("Hote="+nbcheck_hote + '/' + (NbLigne_Hote-1));
		var jh=0;
		var kh=0;
		for (var i=1;i<NbLigne_Hote;i++)
		{
			var tableau_hote_liste_col = tableau_hote_lig[i].cells; // charge les cellules de chaque ligne dans un tableau
			hote_liste[kh] = tableau_hote_liste_col[3].innerHTML + ',';					// Hôte
			hote_liste[kh] += tableau_hote_liste_col[4].innerHTML + ',';				// Description => ajout le 04-11-14
			hote_liste[kh] += tableau_hote_liste_col[5].innerHTML + ',';				// Adresse IP
			hote_liste[kh] += tableau_hote_liste_col[6].innerHTML + ',';				// Controle Atif ou inactif => ajout le 04-11-14
			hote_liste[kh] += tableau_hote_liste_col[7].innerHTML.substring(1) + ',';	// ID_Hote Centreon dépréfixé du h
			hote_liste[kh] += document.getElementById(i).checked;						// Checked true ou false
			if (document.getElementById(i).checked ==true)
			{
				$('input:checked[id=' + i +']').attr("disabled","disabled"); // verrouille l'hôte inséré pour éviter un ajout en double.
				$('input:checked[id=' + i +']').removeAttr("checked"); // décoche l'hôte inséré pour éviter un ajout en double.
			};

			kh++;
/*déprécié le 03-11-2014
 * 			if (nbcheck_hote > 0)
			{
				$('input:checked[id=' + i +']').each(function() // on boucle sur chaque objet sélectionné
				{
					var tableau_hote_col = tableau_hote_lig[i].cells;

					hote_selec[jh] = tableau_hote_col[1].innerHTML + ',';			// Hôte
					hote_selec[jh] += tableau_hote_col[2].innerHTML + ',';			// Description
					hote_selec[jh] += tableau_hote_col[3].innerHTML + ',';			// Adresse IP
					hote_selec[jh] += tableau_hote_col[4].innerHTML + ',';			// Controle actif ou inactif
					hote_selec[jh] += tableau_hote_col[5].innerHTML.substring(1);	// ID_Hote Centreon dépréfixé du h

					$(this).removeAttr("checked"); // décoche l'hôte inséré pour éviter un ajout en double.
					$(this).attr("disabled","disabled"); // verrouille l'hôte inséré pour éviter un ajout en double.
					//$("#client").attr("disabled","disabled");
					jh++;
				}); 
			};
*/
		};
//		hote_selec = hote_selec.join('$');
		hote_liste = hote_liste.join('$');
//		alert("hote_selec="+hote_selec);
//		alert("hote_liste="+hote_liste);
		
///////////////////////////////////////////////////////////////////////
	// boucler sur les services sélectionnés afin de générer une liste
///////////////////////////////////////////////////////////////////////

	var nbcheck_service = $('input:checked[name=selection_service]').size(); // récupère le nombre d'hôte désactivés
	
	//alert("Service="+nbcheck_service + '/' + (NbLigne_Service-1));
	if (nbcheck_service > 0)
	{
		//var tableau_service = new Array();
		var service_selec= new Array();
		//tableau_service = document.getElementById("T_Liste_Service"); // Initialise le tableau des services
		var tableau_service_lig = document.getElementById("T_Liste_Service").rows; //charge les lignes du tableau
		var NbLigne_Service = tableau_service_lig.length; // récupération du nombre d'enregistrement
		var js=0;
		for (var i=1;i<NbLigne_Service;i++)
		{
			$('input:checked[id=s' + i +']').each(function() // on boucle sur chaque objet sélectionné
			{
				var tableau_service_col = tableau_service_lig[i].cells;

				service_selec[js] = tableau_service_col[2].innerHTML + ',';					// Nom du service
				service_selec[js] += tableau_service_col[3].innerHTML + ',';				// Fréquence de controle
				service_selec[js] += tableau_service_col[4].innerHTML + ',';				// Plage Horaire
				service_selec[js] += tableau_service_col[5].innerHTML + ',';				// Controle actif ou inactif
				service_selec[js] += tableau_service_col[6].innerHTML.substring(1) + ',';	// ID_Service Centreon (dépréfixé du s)
				service_selec[js] += tableau_service_col[7].innerHTML.substring(1) + ',';	// ID_Host Centreon (dépréfixé du h)
				service_selec[js] += document.getElementById('s'+i).checked;						// Checked true ou false
				
				$(this).removeAttr("checked"); // décoche le service inséré pour éviter un ajout en double.
				$(this).attr("disabled","disabled"); // verrouille le service inséré pour éviter un ajout en double
				//$("#client").attr("disabled","disabled"); // verrouille le client pour que la demande soit sur une seule prestation
				js++;
			}); 
		};
		service_selec = service_selec.join('$');
		//alert("Service_selec="+service_selec);
	} else
	{
		/**
		 * si aucune sélection, initialise la variable à vide pour ne pas planter l'intégration en base
		 */
		service_selec = "";
	};

		
///////////////////////////////////////////////////////////////////////
	// boucler sur les plages sélectionnés afin de générer une liste
///////////////////////////////////////////////////////////////////////

	//var tableau_plage = new Array();
	var plage_selec= new Array();
	var plage_liste = new Array();
	//tableau_plage = document.getElementById("T_Liste_Plage"); // Initialise le tableau des plages
	var tableau_plage_lig = document.getElementById("T_Liste_Plage").rows; //charge les lignes du tableau
	var NbLigne_Plage = tableau_plage_lig.length; // récupération du nombre d'enregistrement
//	var nbcheck_plage = $('input:checked[name=selection_plage]').size();
	
	//alert("Plage="+nbcheck_plage + '/' + (NbLigne_Plage-1));
//	if (nbcheck_plage > 0)
//	{
	var jp=0;
	var kp=0;
	for (var i=1;i<NbLigne_Plage;i++)
	{
		//if (document.getElementById('p'+i))
		//{
			var tableau_plage_liste_col = tableau_plage_lig[i].cells;
			plage_liste[kp] = tableau_plage_liste_col[1].innerHTML + ';';	// Nom_Plage
			plage_liste[kp] += tableau_plage_liste_col[2].innerHTML + ';';	// Lundi
			plage_liste[kp] += tableau_plage_liste_col[3].innerHTML + ';';	// Mardi
			plage_liste[kp] += tableau_plage_liste_col[4].innerHTML + ';';	// Mercredi
			plage_liste[kp] += tableau_plage_liste_col[5].innerHTML + ';';	// Jeudi
			plage_liste[kp] += tableau_plage_liste_col[6].innerHTML + ';';	// Vendredi
			plage_liste[kp] += tableau_plage_liste_col[7].innerHTML + ';';	// Samedi
			plage_liste[kp] += tableau_plage_liste_col[8].innerHTML + ';';		// Dimanche
			plage_liste[kp] += document.getElementById('p'+i).checked;			// Checked true ou false
			if (document.getElementById('p' + i).checked ==true)
			{
				$('input:checked[id=p' + i +']').attr("disabled","disabled"); // verrouille la plage insérée pour éviter un ajout en double.
				$('input:checked[id=p' + i +']').removeAttr("checked"); // décoche la plage insérée pour éviter un ajout en double.
			};
			kp++;
		//};
/*
		if (nbcheck_plage > 0)
		{
			$('input:checked[id=p' + i +']').each(function() // on boucle sur chaque objet sélectionné
			{
				var tableau_plage_col = tableau_plage_lig[i].cells;

				plage_selec[jp] = tableau_plage_col[1].innerHTML + ',';		// Nom plage
				plage_selec[jp] += tableau_plage_col[2].innerHTML + ',';	// Lundi
				plage_selec[jp] += tableau_plage_col[3].innerHTML + ',';	// Mardi
				plage_selec[jp] += tableau_plage_col[4].innerHTML + ',';	// Mercredi
				plage_selec[jp] += tableau_plage_col[5].innerHTML + ',';	// Jeudi
				plage_selec[jp] += tableau_plage_col[6].innerHTML + ',';	// Vendredi
				plage_selec[jp] += tableau_plage_col[7].innerHTML + ',';	// Samedi
				plage_selec[jp] += tableau_plage_col[8].innerHTML;		// Dimanche
				//plage_selec[jp] += tableau_plage_col[9].innerHTML;			// Commentaire
				//plage_selec[jp] += tableau_plage_col[6].innerHTML.substring(1) + ',';	// ID_Service Centreon (dépréfixé du s)
				//plage_selec[jp] += tableau_plage_col[7].innerHTML.substring(1);			// ID_Host Centreon (dépréfixé du h)

				$(this).removeAttr("checked"); // décoche la plage insérée pour éviter un ajout en double.
				$(this).attr("disabled","disabled"); // verrouille la plage insérée pour éviter un ajout en double
				//$("#clientsup").attr("disabled","disabled"); // verrouille le client pour que la demande soit sur une seule prestation
				//$("#client").removeAttr("class");
				//$("#client").attr("disabled","disabled");

				jp++;
			});
		};
*/
	};
//	plage_selec = plage_selec.join('$');
	plage_liste = plage_liste.join('$');
//alert("plage_selec:"+plage_selec+"-");
//alert("plage_liste:"+plage_liste+"-");
//	};
/////////////////////////////////////////////////////////////////////////
	$("#clientsup").attr("disabled","disabled"); // verrouille le client pour que la demande soit sur une seule prestation
	$("#client").removeAttr("class");
	$("#client").attr("disabled","disabled");
	$("#client_new input:text").attr("disabled","disabled");
	$("a[title='Afficher tout']").remove();
	$("#img_client").attr("src","images/img_ver.png");
				
	insertion_info_gen(affiche_resultat);
	function insertion_info_gen(callback)
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		var loading=false;
		var Statut_Hote;
		var Statut_Plage;
		var Statut_Service;
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
//				collecte_param_global(); // fonction globale de remplissage des hotes plage et service => fonction_remplissage.js
				collecte_liste_hote(); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
				collecte_liste_plage(); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
				collecte_liste_service(); // fonction de remplissage de liste Param Service => fonction_remplissage.js
//				collecte_liste_hote(rempli_hote); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
//				collecte_liste_service(rempli_service); // fonction de remplissage de liste Param Service => fonction_remplissage.js
//				collecte_liste_plage(rempli_plage); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
				$("#img_loading").remove();
				$("#p_loading").remove();
				
				callback(xhr.responseText); // C'est bon \o/
			} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
				$("#img_loading").remove();
				$("#p_loading").remove();
				$("#Rechercher").removeAttr("Disabled");
				$("#Ajouter_Selection_Hote").removeAttr("Disabled");
				$("#Valider_Selection").removeAttr("Disabled");
				// pour chaque input disabled on réactive
				var tableau_service_lig = document.getElementById("T_Liste_Service").rows; //charge les lignes du tableau
				var NbLigne_Service = tableau_service_lig.length; // récupération du nombre d'enregistrement
				for (var i=1;i<NbLigne_Service;i++)
				{
					$('input:disabled[id=s' + i +']').each(function()
					{
						$("#s"+ i ).removeAttr("Disabled");
					});
				};
				var tableau_hote_lig = document.getElementById("T_Liste_Hote").rows; //charge les lignes du tableau
				var NbLigne_Hote = tableau_hote_lig.length; // récupération du nombre d'enregistrement
				for (var i=1;i<NbLigne_Hote;i++)
				{
					$('input:disabled[id=' + i +']').each(function()
					{
						$("#"+ i ).removeAttr("Disabled");
					});
				};
				var tableau_plage_lig = document.getElementById("T_Liste_Plage").rows; //charge les lignes du tableau
				var NbLigne_Plage = tableau_plage_lig.length; // récupération du nombre d'enregistrement
				for (var i=1;i<NbLigne_Plage;i++)
				{
					$('input:disabled[id=p' + i +']').each(function()
					{
						$("#p"+ i ).removeAttr("Disabled");
					});
				};
				gestion_erreur(xhr);
			} else if (loading == false){
				loading=true;
				//alert("patientez!")
				$("#tabs-2").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant le chargement des données..." sssstyle="vertical-align:middle;isplay:inline;"/> ');
				$("#tabs-2").append('<p id="p_loading">Veuillez patienter pendant le chargement des données...</p>');
			};
		};
		
		var sinfo_gen = encodeURIComponent(info_gen);
//		var shote_selec = encodeURIComponent(hote_selec);
		var shote_liste = encodeURIComponent(hote_liste);
		var sservice_selec = encodeURIComponent(service_selec);
//		var splage_selec = encodeURIComponent(plage_selec);
		var splage_liste = encodeURIComponent(plage_liste);
//		alert("info:"+sinfo_gen+"-");
//		alert("hote:"+shote_selec+"-");
//		alert("service:"+sservice_selec+"-");
//		alert("plage:"+splage_selec+"-");
		
		xhr.open("POST", "insertion_selection.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//		xhr.send("info_gen="+sinfo_gen+"&hote_selec="+shote_selec+"&hote_liste="+shote_liste+"&service_selec="+sservice_selec+"&plage_selec="+splage_selec+"&plage_liste="+splage_liste+""); 
//		xhr.send("info_gen="+sinfo_gen+"&hote_liste="+shote_liste+"&service_selec="+sservice_selec+"&plage_selec="+splage_selec+"&plage_liste="+splage_liste+""); 
		xhr.send("info_gen="+sinfo_gen+"&hote_liste="+shote_liste+"&service_selec="+sservice_selec+"&plage_liste="+splage_liste+""); 

	};
	function affiche_resultat(resultat){
		$("#lien_tabs3").removeAttr("style");
		$("#Rechercher").removeAttr("Disabled");
		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
		$("#Valider_Selection").removeAttr("Disabled");
		// retour en haut de la page...
		$('html,body').animate({scrollTop:0},'slow'); // retourne en haut de la page après l'enregistrement
		alert("Chargement terminé! Vous pouvez passer au paramétrage des hôtes et services sur l'onglet paramétrage.");
		$("#lien_tabs3").effect("pulsate");
		//alert(resultat);
	}; 
}; 

function PreEnregistrer_fieldset_hote(champ)
{
	timer_enregistrement();
	var parent=$(champ).parent().parent().attr("id"); // récupèrele fieldset parent hote
	var hote_bouton_id = $(champ).attr("id"); // récupère l'id du bouton
	//alert("id_bouton="+hote_bouton_id);
	var Nom_Hote = $("fieldset#"+parent+ " input[id*='Nom_Hote']").val();
	var IP_Hote = $("fieldset#"+parent+ " input[id*='IP_Hote']").val();
	var MessageConfirmation = "Vous allez pré-enregistrer l'hôte [" + Nom_Hote + "].\n Il sera alors disponible pour le paramétrage des services mais vous ne pourrez plus changer son nom.\n Etes-vous sûr?";
	if (confirm(MessageConfirmation))
	{
		//alert(parent.substring(4));
		var liste_hote = "";
		// enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
		$(".hote" + parent.substring(4) + "").each(function()
		{
			// gestion des caractères spéciaux ! $ et | dans les champs.
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).val() != "Autre" && $(this).val() != "Vide")
			{
				liste_hote += "|" + Valeur_Champ;
			};
		});
		liste_hote += "$";
		liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		//alert("liste_hote="+liste_hote);
		/////////////////////////////////////////////////////////
		// transmettre les données au serveur pour MAJ des infos.
		/////////////////////////////////////////////////////////
		
		function PreEnregistre_Hote(callback)
		{
			var xhr = getXMLHttpRequest(); //création de l'instance XHR
			var loading=false;
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
					$("#img_loading").remove();
					$("#p_loading").remove();
					callback(xhr.responseText); // C'est bon \o/
				} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
					$("#img_loading").remove();
					$("#p_loading").remove();
					$("#"+hote_bouton_id+"").removeAttr("Disabled"); // réactive le bouton
					gestion_erreur(xhr);
				} else if (loading == false){
					loading=true;
					$("fieldset#"+parent+ "").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..." sssssstyle="vertical-align:middle;isplay:inline;"/> ');
					$("fieldset#"+parent+ "").append('<p id="p_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
				};
			};
//			var sliste_plage = encodeURIComponent(liste_plage);
			var sliste_hote = encodeURIComponent(liste_hote);
//			var sliste_service = encodeURIComponent(liste_service);
			//alert("plage:"+sliste_plage + "-");
			//alert("hote:"+sliste_hote + "-");
			//alert("service:"+sliste_service + "-");
			
			xhr.open("POST", "PreEnregistrement_Hote.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
			xhr.send("liste_hote="+sliste_hote+""); 
		};
		function PreEnregistrement_termine(ID_Hote) //récupère la valeur retournée par le script php PreEnregistrement_Hote.php
		{
			$("select[id*='Hote_Service']").each(function()
			{
				//alert($(this).val());
				$(this).append('<option value="'+ ID_Hote +'">'+ Nom_Hote + ' - ' + IP_Hote + '</option>'); // on ajoute à la liste déroulante le nouvel hôte
			});
			var id_input_hote=hote_bouton_id.substring(14);
			$("#Nom"+id_input_hote+"").attr("ReadOnly","ReadOnly"); // passe le champ Nom_Hote en lecture seule
			$("#img_Nom"+id_input_hote+"").attr("src","images/img_ver.png"); // passe le champ im_Nom_Hote en Verrouillé
			$("#"+hote_bouton_id+"").attr("Disabled","Disabled"); // désactive le bouton
			
			alert("Pré-enregistrement de l'hôte terminé!\nCet hôte est désormais disponible dans la configuration des services");
			//$("#Enregistrer_Brouillon").removeAttr("Disabled");
			//$("#Valider_Demande").removeAttr("Disabled");
	//		alert(resultat);
/*			var MessageConfirmation = "Voulez-vous effectuer une nouvelle demande?";
			if (confirm(MessageConfirmation)) 
			{
				include_once('nouvelle_demande.php');
			} else
			{
				include_once('supervision.php');
			};
*/	
		}; 
		PreEnregistre_Hote(PreEnregistrement_termine);
	};
};

function PreEnregistrer_fieldset_plage(champ)
{
	timer_enregistrement();
	var parent=$(champ).parent().parent().parent().attr("id"); // récupèrele fieldset parent plage
	var plage_bouton_id = $(champ).attr("id"); // récupère l'id du bouton
	//	alert(parent);
	var Nom_Plage = $("input#Nom_"+parent).val();
	var MessageConfirmation = "Vous allez pré-enregistrer la plage [" + Nom_Plage + "].\n Elle sera alors disponible pour le paramétrage des services mais vous ne pourrez plus changer son nom.\n Etes-vous sûr?";
	if (confirm(MessageConfirmation))
	{
		//alert(parent.substring(4));
		var liste_plage = "";
		// enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
		$(".plage" + parent.substring(5) + "").each(function()
		{
			// gestion des caractères spéciaux ! $ et | dans les champs.
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).val() != "Autre" && $(this).val() != "Vide")
			{
				//alert($(this).val().innerHTML);
				liste_plage += "|" + Valeur_Champ;
			};
		});
		liste_plage += "$";
		liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		//alert("liste_plage="+liste_plage);
		/////////////////////////////////////////////////////////
		// transmettre les données au serveur pour MAJ des infos.
		/////////////////////////////////////////////////////////
		
		function PreEnregistre_Plage(callback)
		{
			var xhr = getXMLHttpRequest(); //création de l'instance XHR
			var loading=false;
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
					$("#img_loading").remove();
					$("#p_loading").remove();
					callback(xhr.responseText); // C'est bon \o/
				} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
					$("#img_loading").remove();
					$("#"+plage_bouton_id+"").removeAttr("Disabled"); // ré-active le bouton
					gestion_erreur(xhr);
				} else if (loading == false){
					loading=true;
					$("fieldset#"+parent+ "").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..." sssstyle="vertical-align:middle;isplay:inline;"/>');
					$("fieldset#"+parent+ "").append('<p id="p_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
				};
			};
			var sliste_plage = encodeURIComponent(liste_plage);
			
			xhr.open("POST", "PreEnregistrement_Plage.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
			xhr.send("liste_plage="+sliste_plage+""); 
		};
		function PreEnregistrement_plage_termine(resultat)
		{
			$("select[id*='Service_Plage']").each(function()
			{
				$(this).append('<option value="'+ Nom_Plage +'">'+ Nom_Plage +'</option>');
			});
			$("#"+plage_bouton_id+"").attr("Disabled","Disabled"); // désactive le bouton
			alert("Pré-enregistrement de la plage terminé!\nCette plage est désormais disponible dans la configuration des services");
		}; 
		PreEnregistre_Plage(PreEnregistrement_plage_termine);
	};
};

/////////////////////////////////
// Fonction Validation Demande //
/////////////////////////////////
var Doublon = "";
function Valider_Demande()
{

	Hote_Vide=false;
	Plage_Vide=false;
	Modele_Vide=false;

	$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
	$("#Valider_Demande").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...

	var NbFieldset_hote = $("fieldset.hote").length;
	var NbFieldset_service = $("fieldset.service").length;
	var NbFieldset_plage = $("fieldset.plage").length;
	if (NbFieldset_hote == 0 && NbFieldset_service == 0 && NbFieldset_plage == 0){
		alert("Votre demande ne comporte aucun hôte ni service ni période temporelle à traiter. L'enregistrement est arrêté.\nVous devez renseigner au moins un hôte, un service ou une plage horaire pour que la demande soit valide.");
		$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
		$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
		return false;
	};
	controle_doublon();
	if (Doublon != "Oui")
	{
	
		var MessageConfirmation = "Voulez-vous valider votre demande?\nUne fois la demande validée, vous ne pourrez plus la modifier.";
		if (confirm(MessageConfirmation)) 
		{
			var Verif_Param = true;
			var Liste = "";
	
			// vérification de la validité des champs obligatoires
			$("#tabs-3 .verif").each(function(){
				if ($(this).attr("alt") != "correct"){
					Verif_Param = false;
					var ID = $(this).attr("id").substring(4);
					//alert("Liste="+Liste);
		//			alert($("#"+ ID).attr("name"));
					Liste += " - " + $("#"+ ID).attr("name") + '\n';
	
				};
			});
					//alert(Liste);
			if (Verif_Param != true) // Tous les champ ne sont pas vérifiés
			{
				var message="STOP! Tous les champs de l'onglet Paramétrage ne sont pas validés!\nVeuillez vérifier les champs suivants:\n";
				alert(message+Liste);
				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return false;
			};
	//		var liste_valeur = new Array(); // peut également s'écrire: var liste_valeur = [];
	//		var i=0;
	
			//appel de la fonction de récupération des infos générales
			var info_gen = info_generale();
	
		// constitution de la chaine hote
			//var NbFieldset_Hote = $("fieldset.hote").length;
	//		alert("NbFieldset_Hote="+NbFieldset_Hote);
			//var j = 0;
			var liste_hote="";
			//for (var i=1;i<=NbFieldset_Hote;i++)
			$("fieldset[class='hote']").each(function()
			{
				//alert("this="+$(this).attr("id"));
				var class_hote = $(this).attr("id").toLowerCase();
				//alert("class_hote="+class_hote);
				$("."+class_hote+"").each(function()
				//$(".hote" + i + "").each(function()
				{
					// gestion des caractères spéciaux ! $ et | dans les champs.
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
	//					alert($(this).val());
						liste_hote += "|" + Valeur_Champ;
					};
				});
				liste_hote += "$";
			});
			liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
//			alert(liste_hote);
	
		// constitution de la chaine plage
			//var NbFieldset_Plage = $("fieldset.plage").length;
			//alert("NbFieldset_Plage="+NbFieldset_Plage);
			//var j = 0;
			var liste_plage="";
			//for (var i=1;i<=NbFieldset_Plage;i++)
			$("fieldset[class='plage']").each(function()
			{
				//alert("this="+$(this).attr("id"));
				var class_plage = $(this).attr("id").toLowerCase();
				//alert("class_hote="+class_hote);
				$("."+class_plage+"").each(function()
				//$(".plage" + i + "").each(function()
				{
					//if ($(this).val() != "Autre" && $(this).val() != "Vide" && $("#Plage_action" + i + "").val() == "Modifier")
					// gestion des caractères spéciaux ! $ et | dans les champs.
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						//alert("plage="+$(this).val());
						liste_plage += "|" + Valeur_Champ;
					};
				});
				liste_plage += "$";
			});
			liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			//alert(liste_plage);
	
		// constitution de la chaine service
			//var NbFieldset_Service = $("fieldset.service").length;
	//		alert("NbFieldset_Service="+NbFieldset_Service);
			//var j = 0;
			var liste_service="";
			//for (var i=1;i<=NbFieldset_Service;i++)
			$("fieldset[class='service']").each(function()
			{
				//alert("this="+$(this).attr("id"));
				var class_service = $(this).attr("id").toLowerCase();
				//alert("class_hote="+class_hote);
				// création de la chaine service hors arguments
				$("."+class_service+"").each(function()
	//			$(".service" + i + "").each(function()
				{
//					controle_champ_vide($(this),class_service);
					if (($(this).attr("id") == "Hote_Service" + class_service.substring(7)) && ($(this).val() == ""))
					{
						alert("Veuillez sélectionner un hôte pour le service " + class_service.substring(7) +" avant de continuer.");
						Hote_Vide=true;
						return Hote_Vide;
					};
					if (($(this).attr("id") == "Service_Plage" + class_service.substring(7)) && ($(this).val() == ""))
					{
						alert("Veuillez sélectionner une plage de contrôle pour le service " + class_service.substring(7) +" avant de continuer.");
						Plage_Vide=true;
						return Plage_Vide;
					};
					if (($(this).attr("id") == "Service_Modele" + class_service.substring(7)) && ($(this).val() == ""))
					{
						alert("Veuillez sélectionner un modèle pour le service " + class_service.substring(7) +" avant de continuer.");
						Modele_Vide=true;
						return Modele_Vide;
					};

					// gestion des caractères spéciaux !, $, | et \ dans les champs.
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						liste_service += "|" + Valeur_Champ;
					};
				});
	
				var liste_service_Arg = ""; // cette liste est réinitialise pour chaque fieldset service
				$(".Service_Argument" + class_service.substring(7) + "").each(function()
				{
	//				alert("Valeur_Champ avant="+Valeur_Champ);
					// gestion des caractères spéciaux ! $ et | dans les champs.
					var Valeur_Champ_avant =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ_avant);
					//alert("Valeur_Champ apres="+Valeur_Champ);
					liste_service_Arg += "!" + Valeur_Champ;
				});
	
				// correction des seuils disque en occupé si controle via SNMP (Disque!warning!critique)
				correction_seuils_disque(liste_service_Arg);
				
				liste_service += "|" + liste_service_Arg.substring(1) + "$"; // on enlève le premier | des arguments et on ajoute un $ à la fin
				//liste_service += "$";
	
			});
			liste_service = liste_service.substring(1,liste_service.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			//alert(liste_service);
	//	DISK_/	#BEEWARE	#D-S 0h-Minuit	#30 min / 3 min	#actif	#	#	#Modifier	#79!/fghj!85!90
		/////////////////////////////////////////////////////////
		// transmettre les données au serveur pour MAJ des infos.
		/////////////////////////////////////////////////////////
			
			function MAJ_infos_Sondes(callback)
			{
				var xhr = getXMLHttpRequest(); //création de l'instance XHR
				var loading=false;
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						$("#msg_loading").remove();
						$("#img_loading").remove();
						callback(xhr.responseText); // C'est bon \o/
					} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
						$("#msg_loading").remove();
						$("#img_loading").remove();
						$("#Enregistrer_Brouillon").removeAttr("Disabled");
						$("#Valider_Demande").removeAttr("Disabled");
						gestion_erreur(xhr);
					} else if (loading == false){
						loading=true;
						$("#tabs-3").append('<p id="msg_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
						$("#tabs-3").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..." sssstyle="vertical-align:middle;display:inline;"/> ');
					};
				};
				var sinfo_gen = encodeURIComponent(info_gen);
				var sliste_plage = encodeURIComponent(liste_plage);
				var sliste_hote = encodeURIComponent(liste_hote);
				var sliste_service = encodeURIComponent(liste_service);
	//			alert("plage:"+sliste_plage + "-");
	//			alert("hote:"+sliste_hote + "-");
	//			alert("service:"+sliste_service + "-");
				
				xhr.open("POST", "enregistrement_Demande.php", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
				xhr.send("info_gen="+sinfo_gen+"&liste_plage="+sliste_plage+"&liste_hote="+sliste_hote+"&liste_service="+sliste_service+""); 
			};
			function enregistrement_termine(resultat){
				var ref_demande = $("#ref_demande").val();
				alert("Enregistrement de la demande [" + ref_demande + "] terminé!\nVous pouvez consulter l'état des demandes via le menu <Lister les demandes>.\nVous recevrez une confirmation d'enregistrement d'ici quelques minutes par l\'intermédiaire de SUSI avec le numéro de ticket correspondant.");
				$("#Enregistrer_Brouillon").removeAttr("Disabled");
				$("#Valider_Demande").removeAttr("Disabled");
					window.location.replace('index.php'); // recharge la page d'accueil
			}; 
			if ((Hote_Vide == false) && (Plage_Vide == false) && (Modele_Vide == false)) //si hote, plage et modele sont rempli on met à jour
			{
				MAJ_infos_Sondes(enregistrement_termine);
			} else
			{
				$("#Enregistrer_Brouillon").removeAttr("Disabled");
				$("#Valider_Demande").removeAttr("Disabled");
				return false;
			};
		} else
		{
			$("#Enregistrer_Brouillon").removeAttr("Disabled");
			$("#Valider_Demande").removeAttr("Disabled");
		};
	};
};

/////////////////////////////////
// Fonction Enregistre Brouillon //
/////////////////////////////////
function Enregistrer_Brouillon(Bouton)
{

	Hote_Vide=false;
	Plage_Vide=false;
	Modele_Vide=false;
	
//	var Valide = controle_doublon();
//	if (Valide == false)
//	{
//		return false;
//	};
	controle_doublon();
	if (Doublon != "Oui")
	{
//		if (($("#lien_tabs3").css("Visibility") == "visible") || ($("#lien_tabs3").css("Visibility") == "undefined")) // "visible" sur nouvelle demande, "undefined" sur une reprise 
		if ($("#lien_tabs3").css("Visibility") == "visible")
		{
			//alert("Bouton="+Bouton);
			function Traitement_Enregistre()
			{
				var Liste = "";
	//			var liste_valeur = new Array(); // peut également s'écrire: var liste_valeur = [];
	//			var i=0;
				//appel de la fonction de récupération des infos générales
				var info_gen = info_generale();
			
				// constitution de la chaine hote
				//var NbFieldset_Hote = $("fieldset.hote").length;
				//alert("NbFieldset_Hote="+NbFieldset_Hote);
				//var j = 0;
				var liste_hote="";
				//var liste_ID =  $("[id^='Hote']").val();
				//alert("liste_ID="+liste_ID);
	//			for (var i=1;i<=NbFieldset_Hote;i++)
	//			for (var i=0;i<NbFieldset_Hote;i++)
				$("fieldset[class='hote']").each(function()
				{
					//alert("this="+$(this).attr("id"));
					var class_hote = $(this).attr("id").toLowerCase();
					//alert("class_hote="+class_hote);
					$("."+class_hote+"").each(function()
					{
						// gestion des caractères spéciaux ! $ et | dans les champs.
						var Valeur_Champ =  $(this).val();
						Gestion_caractere_speciaux(Valeur_Champ);
						if ($(this).val() != "Autre" && $(this).val() != "Vide")
						{
								//alert($(this).val());
								//alert("Valeur_Champ="+Valeur_Champ);
							liste_hote += "|" + Valeur_Champ;
						};
					});
					liste_hote += "$";
				});
				liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
				liste_hote = liste_hote.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
				//alert(liste_hote);
				// constitution de la chaine plage
				//var NbFieldset_Plage = $("fieldset.plage").length;
				//alert("NbFieldset_Plage="+NbFieldset_Plage);
				//var j = 0;
				var liste_plage="";
				//for (var i=1;i<=NbFieldset_Plage;i++)
				$("fieldset[class='plage']").each(function()
				{
					var class_plage = $(this).attr("id").toLowerCase();
					$("."+class_plage+"").each(function()
					{
						// gestion des caractères spéciaux ! $ et | dans les champs.
						var Valeur_Champ =  $(this).val();
						Gestion_caractere_speciaux(Valeur_Champ);
						if ($(this).val() != "Autre" && $(this).val() != "Vide")
						{
							//alert("plage="+$(this).val());
							liste_plage += "|" + Valeur_Champ;
						};
					});
					liste_plage += "$";
				});
				liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
				liste_plage = liste_plage.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
				//alert(liste_plage);
				// constitution de la chaine service
				//var NbFieldset_Service = $("fieldset.service").length;
				//alert("NbFieldset_Service="+NbFieldset_Service);
				//var j = 0;
				var liste_service="";
				//for (var i=1;i<=NbFieldset_Service;i++)
				$("fieldset[class='service']").each(function()
				{
					// création de la chaine service hors arguments
					var class_service = $(this).attr("id").toLowerCase();
					//alert("class_service="+ class_service + " et numero=" + class_service.substring(7));
					$(".service" + class_service.substring(7) + "").each(function()
					//$(".service" + i + "").each(function()
					{
						//alert("bis class_service="+ class_service + " et numero=" + class_service.substring(7));
//						controle_champ_vide($(this),class_service);
						if (($(this).attr("id") == "Hote_Service" + class_service.substring(7)) && ($(this).val() == ""))
						{
							alert("Veuillez sélectionner un hôte pour le service " + class_service.substring(7) +" avant de continuer.");
							Hote_Vide=true;
							return Hote_Vide;
						};
						if (($(this).attr("id") == "Service_Plage" + class_service.substring(7)) && ($(this).val() == ""))
						{
							alert("Veuillez sélectionner une plage de contrôle pour le service " + class_service.substring(7) +" avant de continuer.");
							Plage_Vide=true;
							return Plage_Vide;
						};
						if (($(this).attr("id") == "Service_Modele" + class_service.substring(7)) && ($(this).val() == ""))
						{
							alert("Veuillez sélectionner un modèle pour le service " + class_service.substring(7) +" avant de continuer.");
							Modele_Vide=true;
							return Modele_Vide;
						};

						// gestion des caractères spéciaux ! $ et | dans les champs.
						var Valeur_Champ =  $(this).val();
						//alert("Valeur_champ=" + Valeur_Champ);
						Gestion_caractere_speciaux(Valeur_Champ);
						if ($(this).val() != "Autre" && $(this).val() != "Vide")
						{
							liste_service += "|" + Valeur_Champ;
						};
					});
					//alert("liste_service="+ liste_service);
					var liste_service_Arg = ""; // cette liste est réinitialise pour chaque fieldset service
					//$(".Service_Argument" + i + "").each(function()
					$(".Service_Argument" + class_service.substring(7) + "").each(function()
					{
						// gestion des caractères spéciaux ! $ et | dans les champs.
						var Valeur_Champ_avant =  $(this).val();
						Gestion_caractere_speciaux(Valeur_Champ_avant);
						//alert("Valeur_Champ="+Valeur_Champ);
						liste_service_Arg += "!" + Valeur_Champ;
					});
					//alert("liste_service_Arg="+liste_service_Arg);
	
					// correction des seuils disque en occupé si controle via SNMP (Disque!warning!critique)
					correction_seuils_disque(liste_service_Arg);
					
					liste_service += "|" + liste_service_Arg.substring(1) + "$"; // on enlève le premier ! des arguments et on ajoute un $ à la fin
					//liste_service += "$"; // désactivé le 11/05/14 je ne sais plus pourquoi j'en ajoutais un; considère alors qu'il existe un fieldset vide à ajouter :(
	
				});
				liste_service = liste_service.substring(1,liste_service.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
				liste_service = liste_service.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
				//alert(liste_service);
				/////////////////////////////////////////////////////////
				// transmettre les données au serveur pour MAJ des infos.
				/////////////////////////////////////////////////////////
				function MAJ_Brouillon(callback)
				{
					var xhr = getXMLHttpRequest(); //création de l'instance XHR
					var loading=false;
					xhr.onreadystatechange = function() {
						if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
							$("#msg_loading").remove();
							$("#img_loading").remove();
							callback(xhr.responseText); // C'est bon \o/
						} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
							$("#msg_loading").remove();
							$("#img_loading").remove();
							$("#Enregistrer_Brouillon").removeAttr("Disabled");
							$("#Valider_Demande").removeAttr("Disabled");
							gestion_erreur(xhr);
						} else if (loading == false){
							loading=true;
							$("#tabs-3").append('<p id="msg_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
							$("#tabs-3").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..." sssstyle="vertical-align:middle;isplay:inline;"/> ');
						};
					};
					var sinfo_gen = encodeURIComponent(info_gen);
					var sliste_plage = encodeURIComponent(liste_plage);
					var sliste_hote = encodeURIComponent(liste_hote);
					var sliste_service = encodeURIComponent(liste_service);
					//alert("plage:"+sliste_plage + "-");
					//alert("hote:"+sliste_hote + "-");
					//alert("service:"+sliste_service + "-");
					
					xhr.open("POST", "enregistrement_Brouillon.php", true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
					xhr.send("info_gen="+sinfo_gen+"&liste_plage="+sliste_plage+"&liste_hote="+sliste_hote+"&liste_service="+sliste_service+""); 
				};
				if ((Hote_Vide == false) && (Plage_Vide == false) && (Modele_Vide == false)) //si hote, plage et modele sont rempli on met à jour
				{
					MAJ_Brouillon(enregistrement_brouillon);
				} else
				{
					$("#Enregistrer_Brouillon").removeAttr("Disabled");
					$("#Valider_Demande").removeAttr("Disabled");
					return false;
				};
				function enregistrement_brouillon(resultat){
					alert("Enregistrement du brouillon terminé!");
					$("#Enregistrer_Brouillon").removeAttr("Disabled");
					$("#Valider_Demande").removeAttr("Disabled");
					//alert(resultat);
				}; 
				
			};
			//alert("Bouton="+Bouton);
			if (Bouton == true)
			{
				$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
				$("#Valider_Demande").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
// 06/10/15 désactivation message de confirmation.
//				var MessageConfirmation = "Voulez-vous enregistrer cette demande comme brouillon?\nElle ne sera pas traiter tant que vous ne l'aurez pas validée\n, vous pourrez la modifier à votre convenance via le menu \"Demandes en cours\".";
//				if (confirm(MessageConfirmation)) 
//				{
					Traitement_Enregistre();
//				} else
//				{
//					$("#Enregistrer_Brouillon").removeAttr("Disabled");
//					$("#Valider_Demande").removeAttr("Disabled");
//				};
// Désactivation le 18/06/15
//			} else
//			{
//				Traitement_Enregistre();
			};
		};
	};
};

function enregistre_Etat_Demande(champ,ID)
{ // fonction d'enregistrement de l'état du paramétrage pour le fieldset hote, service ou plage en cours => passe la demande globale au statut "En cours" si ce n'est pas le cas.
	/**
	 * ID correspond à l'ID_Hote, l'ID_Service ou l'ID_periode selon le cas
	 */
	var parent=$(champ).parent().parent().parent().attr("id"); // récupèrele fieldset parent DEC_hote contenant l'ID_Demande
	var ID_Hote = "";
	var ID_Service = "";
	var ID_Plage = "";
	//alert(champ.id.substring(12));
	var Etat_Param = $("Select#"+champ.id.substring(12)).val();
	if (Etat_Param == "Brouillon")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_brou");
	} else if (Etat_Param == "A Traiter")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_atra");
	} else if (Etat_Param == "En cours")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_enco");
	} else if (Etat_Param == "Validation")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_vali");
	} else if (Etat_Param == "Traité")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_trai");
	} else if (Etat_Param == "Annulé")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_annu");
	} else if (Etat_Param == "Supprimer")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_supp");
	};
	//	alert("DEC_parent="+parent);
	if (parent.indexOf('hote')>0) // si c'est un hote
	{
//		alert("ID_Dem="+parent.substring(8));
//		alert("ID_Hote="+ID);
		var ID_Demande = parent.substring(8);
		ID_Hote = ID;
	} else if (parent.indexOf('service')>0) // si c'est un service
	{
//		alert("ID_Dem="+parent.substring(11));
//		alert("ID_Service="+ID);
		var ID_Demande = parent.substring(11);
		ID_Service = ID;
	} else if (parent.indexOf('plage')>0) // si c'est une plage
	{
//		alert("ID_Dem="+parent.substring(9));
//		alert("ID_Plage="+ID);
		var ID_Demande = parent.substring(9);
		ID_Plage = ID;
	};

	//alert('ID_Demande='+ID_Demande+'\nID_Hote='+ID_Hote+'\nID_Service='+ID_Service+'\nID_Plage='+ID_Plage+'\nEtat_Param='+Etat_Param);

	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			// version 8.11
			var Etat_dem=$("#Liste_DEC_Enregistrer_Etat"+ID_Demande).val();
			//alert("Etat_dem="+Etat_dem+" Etat_Param="+Etat_Param);
			if (Etat_Param == "En cours" && Etat_dem == "A Traiter")
			{
				window.location.reload(); // rechargement de la page pour afficher le statut "en cours"
			};
			
			alert("Mise à jour Etat paramétrage OK!");
		} else if(xhr.readyState == 4 && xhr.status != 200) 
		{ // En cas d'erreur !
			gestion_erreur(xhr);
		};
	};
	/**
	 * déclaration nouvelles variables encodee (prefixée e)
	 */
	var eID_Demande = encodeURIComponent(ID_Demande);
	var eID_Hote = encodeURIComponent(ID_Hote);
	var eID_Service = encodeURIComponent(ID_Service);
	var eID_Plage = encodeURIComponent(ID_Plage);
	var eEtat_Param = encodeURIComponent(Etat_Param);
	
	xhr.open("POST", "MAJ_Etat_Parametrage.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("ID_Demande="+eID_Demande+"&ID_Hote="+eID_Hote+"&ID_Service="+eID_Service+"&ID_Plage="+eID_Plage+"&Etat_Param="+eEtat_Param+""); 

};

function DEC_enregistre_Etat_Demande(champ,ID_Demande)
{ // fonction d'enregistrement de l'état du paramétrage pour l'ensemble de la demande en cours => Tous les hôtes services et plages seront forcés dans l'état choisi ainsi que la demande.
	var Etat_Param = $("Select#Liste_"+champ.id).val();

	if (Etat_Param != "Supprimer")
	{
		var MessageConfirmation = "Vous allez forcer l'ensemble des sondes de la demande n°" + ID_Demande + " dans l'état [" + Etat_Param + "]. Etes-vous sûr?";
	} else
	{
		var MessageConfirmation = "Vous allez définitivement supprimer la demande n°" + ID_Demande + " ainsi que tous les hôtes, plages et services associés. Etes-vous sûr?";
	};
	if (confirm(MessageConfirmation)) 
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
/**
 * Désactivation demande rechargement => rechargement obligatoire avec l'activation des mails auto.
 */
//				var MessageRecharger = "Voulez-vous recharger la page?";
//				if (confirm(MessageRecharger)) 
//				{
//					//alert("Mise à jour Etat demande OK! Rechargement de la page pour mise à jour de la liste.");
				// version 8.11	
				//window.location.replace('lister_demande.php'); // si OK => recharge la page lister demande
				window.location.reload(); // si OK => recharge la page lister demande
//				};
			} else if(xhr.readyState == 4 && xhr.status != 200) 
			{ // En cas d'erreur !
				gestion_erreur(xhr);
			};
		};

		ID_Demande = encodeURIComponent(ID_Demande);
		Etat_Param = encodeURIComponent(Etat_Param);
		
		xhr.open("POST", "MAJ_Etat_Parametrage_Demande.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("ID_Demande="+ID_Demande+"&Etat_Param="+Etat_Param+""); 
	};
};

function Enregistre_Notif_BAM()
{
	/**
	 * Fonction d'enregistrement de la configuration des notifications BAM
	 */
	var Verif_Info = true;
//	$("#info > input[type=text],textarea,select").each(function()
	$("#field_config_notification .verif").each(function(){
		//alert($(this).attr("alt"));
		if ($(this).attr("alt") != "correct"){
			Verif_Info = false;
		};
	});

	if (Verif_Info != true) // l'onglet info générale doit contenir 7 valeurs impérativement
	{
		var message="STOP! Tous les champs ne sont pas valides!";
		alert(message);
//		$("#Rechercher").removeAttr("Disabled");
//		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
//		$("#Valider_Selection").removeAttr("Disabled");
		//$("#lien_tabs2").attr("style","visibility: hidden");
		//$("#lien_tabs3").attr("style","visibility: hidden");
		return false;
	};

//	var parent=$(champ).parent().parent().attr("id"); // récupèrele fieldset parent hote
//	var hote_bouton_id = $(champ).attr("id"); // récupère l'id du bouton
//	//alert("id_bouton="+hote_bouton_id);
//	var Nom_Hote = $("fieldset#"+parent+ " input[id*='Nom_Hote']").val();
//	var IP_Hote = $("fieldset#"+parent+ " input[id*='IP_Hote']").val();
//	var MessageConfirmation = "Vous allez pré-enregistrer l'hôte [" + Nom_Hote + "].\n Il sera alors disponible pour le paramétrage des services mais vous ne pourrez plus changer son nom.\n Etes-vous sûr?";
//	if (confirm(MessageConfirmation))
//	{
		//alert(parent.substring(4));
//		var liste_hote = "";
	var liste_conf = "";
//		//enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
//		$(".hote" + parent.substring(4) + "").each(function()
	$(".gb_config").each(function()
	{
		// gestion des caractères spéciaux ! $ et | dans les champs.
		var Valeur_Champ =  $(this).val();
		//alert("Valeur_Champ="+Valeur_Champ);
		//Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).attr("type")=="checkbox")
			{
				//alert("checkbox="+$(this).is(':checked'));
				if ($(this).is(':checked'))
				{
					liste_conf += "|" + '1';// jour coché				
				} else
				{
					liste_conf += "|" + '0';// jour non coché
				};
			} else
			{
				liste_conf += "|" + Valeur_Champ;
			};
	});
	var select_am = "";
	//alert($("select[id='am_associe'] option").val());
	$("select[id='am_associe'] option").each(function() {
		//select_am += $("select[id='am_associe'] option").val() + ';';
		//select_am += $(this).val() + '$';
		select_am += $(this).val().substring(0,$(this).val().indexOf("_")) + '$';
	});
	/**
	 * enlève le dernier ; de la chaine select_am
	 */
	select_am = select_am.substring(0,select_am.length-1);
	//alert("select_am="+select_am);
	liste_conf += "|" + select_am;
	//liste_conf = liste_conf.substring(1,liste_conf.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
	liste_conf = liste_conf.substring(1); // enlève le premier "|"
	//alert("liste_conf="+liste_conf);
	/////////////////////////////////////////////////////////
	// transmettre les données au serveur pour MAJ des infos.
	/////////////////////////////////////////////////////////
	
//	function Enregistre_conf(callback)
//	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		var loading=false;
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
				$("#img_loading").remove();
				$("#p_loading").remove();
				//callback(xhr.responseText); // C'est bon \o/
				alert("enregistrement terminé.");
				//window.location.reload();
			} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
				$("#img_loading").remove();
				$("#p_loading").remove();
				$("#"+hote_bouton_id+"").removeAttr("Disabled"); // réactive le bouton
				gestion_erreur(xhr);
			} else if (loading == false){
				loading=true;
				$("#field_config_notification").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..."/> ');
				$("#field_config_notification").append('<p id="p_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
			};
		};
//			var sliste_plage = encodeURIComponent(liste_plage);
		var sliste_conf = encodeURIComponent(liste_conf);
//			var sliste_service = encodeURIComponent(liste_service);
			//alert("plage:"+sliste_plage + "-");
			//alert("hote:"+sliste_hote + "-");
			//alert("service:"+sliste_service + "-");
			
		xhr.open("POST", "BAM_enregistre_conf.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("liste_conf="+sliste_conf+""); 
//	};
//		function PreEnregistrement_termine(ID_Hote) //récupère la valeur retournée par le script php PreEnregistrement_Hote.php
//		{
//			$("select[id*='Hote_Service']").each(function()
//			{
//				//alert($(this).val());
//				$(this).append('<option value="'+ ID_Hote +'">'+ Nom_Hote + ' - ' + IP_Hote + '</option>'); // on ajoute à la liste déroulante le nouvel hôte
//			});
//			$("#"+hote_bouton_id+"").attr("Disabled","Disabled"); // désactive le bouton
//			alert("Pré-enregistrement de l'hôte terminé!\nCet hôte est désormais disponible dans la configuration des services");
//			//$("#Enregistrer_Brouillon").removeAttr("Disabled");
//			//$("#Valider_Demande").removeAttr("Disabled");
//	//		alert(resultat);
///*			var MessageConfirmation = "Voulez-vous effectuer une nouvelle demande?";
//			if (confirm(MessageConfirmation)) 
//			{
//				include_once('nouvelle_demande.php');
//			} else
//			{
//				include_once('supervision.php');
//			};
//*/	
//		}; 
//	Enregistre_conf();
};
function set_focus_bouton(ID_bouton)
{ // fonction pour positionner le focus sur le bouton "Forcer"
//	var Focus_ID_Bouton = 'DEC_Enregistrer_Etat' + ID_bouton;
	var Focus_ID_Bouton = ID_bouton;
	//alert(Focus_ID_Bouton);
	//document.getElementById(Focus_ID_Bouton).focus();
	$("#" + Focus_ID_Bouton + "").focus();
};

function clic_recherche_hote(evenement)
{
	var touche = window.event ? evenement.keyCode : evenement.which; // si IE => keyCode, si Gecko(firefox)=> which
    if (touche == 13) // si touche Entrée (code 13)
    {
    	charger_liste_recherche_hote();
    };
};

function Gestion_caractere_speciaux(str)
{
	if (typeof str == 'undefined') // gestion des cas de fieldset hote ou service supprimés avant le dernier
	{
		var str ="";
	};
	//alert("str="+str);
	// remplacement de tous les antislash par des slash
	str = str.replace(/\\/g,"\/");
	
	var reg1=new RegExp("[|]","g");
	if (str.match(reg1))
	{
		str = str.replace(/|/g,"_PIPE_");
	};
	var reg1=new RegExp("[ ]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\ /g,"_ESP_");
	};
	var reg1=new RegExp("[$]","g");
	if (str.match(reg1))
	{
		str = str.replace(/$/g,"_DOLLAR_");
	};
	var reg1=new RegExp("[!]","g");
	if (str.match(reg1))
	{
		str = str.replace(/!/g,"_PEX_");
	};
	var reg1=new RegExp("[(]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\(/g,"_PO_");
	};
	var reg1=new RegExp("[)]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\)/g,"_PF_");
	};
	var reg1=new RegExp("[#]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\#/g,"_DIESE_");
	};
	var reg1=new RegExp("[*]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\*/g,"_ETOIL_");
	};
	var reg1=new RegExp("[']","g");
	if (str.match(reg1))
	{
		str = str.replace(/\'/g,"_SQUOTE_");
	};
	var reg1=new RegExp("[\"]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\"/g,"_DQUOTE_");
	};
//	var reg1=new RegExp("[\]","g");
//	if (str.match(reg1))
//	{
//		str = str.replace(/\\/g,"_BCKSL_");
//	};
	var str2 = "";
	str2 = encodeURI(str);
	Valeur_Champ = str2;
	//alert("Valeur_Champ="+Valeur_Champ);
	//return Valeur_Champ;
};

function info_generale()
{
	var info = "";
	var liste_valeur = new Array(); // peut également s'écrire: var liste_valeur = [];
	var i=0;
	$(".info_generale").each(function()
	{ 
		if ($(this).val() && ($(this).val() != "Nouveau" || $(this).val() != ""))
		{
			// gestion des caractères spéciaux ! $ et | dans les champs.
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).attr("id") == "client_new")
			{
				liste_valeur[i] = "NEW_" + Valeur_Champ;
			} else
			{
				liste_valeur[i] = Valeur_Champ;
			};
			//alert("-"+liste_valeur[i]+"-");
			i++;
		};
	});
	if (!liste_valeur[6]) // si le champ email est vide on le rend non null.
	{
		liste_valeur[6]=" ";
	};
	if (!liste_valeur[7]) // si le champ commentaire a été vidé on le rend non null.
	{
		liste_valeur[7]=" ";
	};
	
	info = liste_valeur.join('$'); // permet de convertir le tableau en une chaine séparée par un $ ce qui permet d'accepter la ponctuation courante dans le champ commentaire
		//alert(info);
	return info;
};

function controle_doublon()
{
	// Vérification des doublons d'hôtes et services et plages
	// constitution de la chaine hote
	var NbFieldset_Hote = $("fieldset.hote").length;
	//alert("NbFieldset_Hote="+NbFieldset_Hote);
	var j = 0;
	var liste_nom_hote="";
	for (var i=1;i<=NbFieldset_Hote;i++)
	{
		// gestion des caractères spéciaux ! $ et | dans les champs.
		var Valeur_Champ =  $("#Nom_Hote" + i + "").val();
                // Si trou dans la liste on passe au suivant
                if (typeof(Valeur_Champ) != 'undefined')
                {
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			var Valeur_Champ_IP =  $("#IP_Hote" + i + "").val();
			Gestion_caractere_speciaux(Valeur_Champ_IP);
			Valeur += " " + Valeur_Champ_IP;
			//alert("Valeur="+Valeur);
	
			//if ($(this).val() != "Autre" && $(this).val() != "Vide")
			//{
				//alert($(this).val());
				liste_nom_hote += "|" + Valeur;
			//};
		};
	};
	liste_nom_hote = liste_nom_hote.substring(1);
	//alert("liste_nom_hote="+liste_nom_hote);
	var T_liste_nom_hote = liste_nom_hote.split("|");
	//alert("lng="+T_liste_nom_hote.length);
	// boucle sur chaque enregistrement de la liste pour trouver les doublons.
	for (var i=0;i<T_liste_nom_hote.length;i++)
	{
		//alert("hote source="+T_liste_nom_hote[i]);
		for (var j=0;j<i;j++)
		{
		//alert("hote cible="+T_liste_nom_hote[j]);
			if (T_liste_nom_hote[i] == T_liste_nom_hote[j])
			{
				alert("ATTENTION: L'hôte ["+T_liste_nom_hote[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon="Oui";
				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return Doublon; // on arrête l'enregistrement du brouillon.
				//break;
			};
		};
	};
	// constitution de la chaine plage
	var NbFieldset_Plage = $("fieldset.plage").length;
	//alert("NbFieldset_Plage="+NbFieldset_Plage);
	var j = 0;
	var liste_nom_plage="";
	for (var i=1;i<=NbFieldset_Plage;i++)
	{
		// gestion des caractères spéciaux ! $ et | dans les champs.
		var Valeur_Champ =  $("#Nom_Plage" + i + "").val();
                // Si trou dans la liste on passe au suivant
                if (typeof(Valeur_Champ) != 'undefined')
                {
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			//alert("Valeur="+Valeur);
	
			//if ($(this).val() != "Autre" && $(this).val() != "Vide")
			//{
				//alert($(this).val());
				liste_nom_plage += "|" + Valeur;
			//};
		};
	};
	liste_nom_plage = liste_nom_plage.substring(1);
	//alert("liste_nom_plage="+liste_nom_plage);
	var T_liste_nom_plage = liste_nom_plage.split("|");
	//alert("lng="+T_liste_nom_plage.length);
	// boucle sur chaque enregistrement de la liste pour trouver les doublons.
	for (var i=0;i<T_liste_nom_plage.length;i++)
	{
		//alert("hote source="+T_liste_nom_hote[i]);
		for (var j=0;j<i;j++)
		{
		//alert("hote cible="+T_liste_nom_hote[j]);
			if (T_liste_nom_plage[i] == T_liste_nom_plage[j])
			{
				alert("ATTENTION: La plage ["+T_liste_nom_plage[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon = "Oui";
				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return Doublon; // on arrête l'enregistrement du brouillon.
			};
		};
	};

	// constitution de la chaine service
	var NbFieldset_Service = $("fieldset.service").length;
	//alert("NbFieldset_Service="+NbFieldset_Service);
	var j = 0;
	var liste_nom_service="";
	for (var i=1;i<=NbFieldset_Service;i++)
	{
		// gestion des caractères spéciaux ! $ et | dans les champs.
		var Valeur_Champ =  $("#Nom_Service" + i + "").val();
		var Valeur_Champ_Hote =  $("#Hote_Service" + i + " option:selected").text();
		//alert("Service" + i + "="+Valeur_Champ);
		//alert("Hote" + i + "="+Valeur_Champ_Hote);
		
		// Si trou dans la liste on passe au suivant
		if (typeof(Valeur_Champ) != 'undefined') 
		{
			//alert("Service="+Valeur_Champ);
			//alert("Hote="+Valeur_Champ_Hote);
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			Gestion_caractere_speciaux(Valeur_Champ_Hote);
			Valeur += " sur " + Valeur_Champ_Hote;
			//alert("Valeur="+Valeur);
			//if ($(this).val() != "Autre" && $(this).val() != "Vide")
			//{
				//alert($(this).val());
				//liste_nom_service += "|" + Valeur_Champ;
				liste_nom_service += "|" + Valeur;
			//};
		};
	};
	liste_nom_service = liste_nom_service.substring(1);
	//alert("liste_nom_service="+liste_nom_service);
	var T_liste_nom_service = liste_nom_service.split("|");
	//alert("lng="+T_liste_nom_service.length);
	// boucle sur chaque enregistrement de la liste pour trouver les doublons.
	for (var i=0;i<T_liste_nom_service.length;i++)
	{
		//alert("service source="+T_liste_nom_service[i]);
		for (var j=0;j<i;j++)
		{
		//alert("service cible="+T_liste_nom_service[j]);
			if (T_liste_nom_service[i] == T_liste_nom_service[j])
			{
				alert("ATTENTION: Le service ["+T_liste_nom_service[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon = "Oui";
				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return Doublon; // on arrête l'enregistrement du brouillon.
			};
		};
	};
	Doublon = "Non";
	return Doublon;
};

function correction_seuils_disque(liste_service_Arg)
{
	//var reg1=new RegExp("^\![a-zA-Z]%3A","g");
	var reg1=new RegExp("^![a-zA-Z]{1}:!"); // si la chaine d'argument commence par "!<Lettre de lecteur>:!"
	var reg2=new RegExp("^!/[a-zA-Z/]*!"); // si la chaine d'argument commence par "!/<montage>!"
	//				if (liste_service_Arg.match(reg1) || liste_service_Arg.match(reg2))
	if ((liste_service_Arg.match(reg1)) || (liste_service_Arg.match(reg2))) // si la chaine contient l'une ou l'autre des expressions...
	{
		// redécoupe de la chaine pour traitement des seuils
		//alert("liste_service_Arg="+liste_service_Arg.substring(1));
		var T_T_liste_Arg = liste_service_Arg.substring(1).split("!");
		for (var j=1;j<3;j++)
		{
			//alert("pourcent="+T_T_liste_Arg[j].substring(T_T_liste_Arg[j].length-3));
			if (T_T_liste_Arg[j].substring(T_T_liste_Arg[j].length-3) == "%25") // conversion uniquement si le seuil est en %
			{
				T_T_liste_Arg[j] = T_T_liste_Arg[j].replace(T_T_liste_Arg[j].substring(0,2),100-T_T_liste_Arg[j].substring(0,2));
				//alert("resultat="+T_T_liste_Arg[j]);
			};
		};
		liste_service_Arg = "!"+ T_T_liste_Arg[0] + "!" + T_T_liste_Arg[1] + "!"+ T_T_liste_Arg[2];
		//alert("chaine arg="+liste_service_Arg);
	};
};

//function controle_champ_vide(champ,class_service)
//{
//	if ((champ.attr("id") == "Hote_Service" + class_service.substring(7)) && (champ.val() == ""))
//	{
//		alert("Veuillez sélectionner un hôte pour le service " + class_service.substring(7) +" avant de continuer.")
//		Hote_Vide=true;
//		return Hote_Vide;
//	} else
//	{
//		Hote_Vide=false;
//		return Hote_Vide;
//	};
//	if ((champ.attr("id") == "Service_Plage" + class_service.substring(7)) && (champ.val() == ""))
//	{
//		alert("Veuillez sélectionner une plage de contrôle pour le service " + class_service.substring(7) +" avant de continuer.")
//		Plage_Vide=true;
//		return Plage_Vide;
//	} else
//	{
//		Plage_Vide=false;
//		return Plage_Vide;
//	};
//
//};

function timer_enregistrement()
{
	/**
	 * Fonction permettant de vérifier la date du dernier brouilon enregistré dans la session
	 * si délai supérieur à 10 minutes => proposition d'enregsitrer le brouillon
	 */
	function recuperation_Timer(callback)
	{
		var xhr_timer = getXMLHttpRequest(); //création de l'instance XHR
		var loading=false;
		xhr_timer.onreadystatechange = function() {
			if (xhr_timer.readyState == 4 && (xhr_timer.status == 200 || xhr_timer.status == 0)) {
				callback(xhr_timer.responseText); // C'est bon \o/
				//alert(xhr_timer.responseText);
			} else if(xhr_timer.readyState == 4 && xhr_timer.status != 200) { // En cas d'erreur !
				gestion_erreur(xhr_timer);
			};
		};
		
		xhr_timer.open("POST", "recuperation_Timer.php", true);
		xhr_timer.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_timer.send(); 
	};
	function avertissement_Timer(Delai_Timer) //récupère la valeur retournée par le script php PreEnregistrement_Hote.php
	{
		//alert("Delai_Timer="+Delai_Timer);
		if (Delai_Timer > 900)
		{
			alert("Cela fait plus de 15 minutes que rien n'a été enregistré en base.\nJe vous conseille vivement d'enregistrer votre brouillon maintenant.");
		};
	}; 
	recuperation_Timer(avertissement_Timer);
};

function gestion_erreur(xhr)
{
	alert('Une erreur est survenue !\nVeuillez re-essayer. Si le problème persiste, notez le message ci-dessous et contactez l\'administrateur.\nCode:' + xhr.status + '\nNature de l\'erreur: ' + xhr.responseText);	
};