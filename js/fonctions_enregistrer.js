/**
 * Déclaration des variables globales
 */
var Doublon = "";
var PreEnregistre= true;
var listeBouton="";

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
		listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
		activeBouton(listeBouton);
//		$("#Rechercher").removeAttr("Disabled");
//		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
//		$("#Valider_Selection").removeAttr("Disabled");
		$("#lien_tabs2").attr("style","visibility: hidden");
		$("#lien_tabs3").attr("style","visibility: hidden");
		$('html,body').animate({scrollTop:0}, 'slow'); // retourne en haut de la page
		return false;
	} else
	{
		$("#lien_tabs2").removeAttr("style");
		$("#lien_tabs2").effect("pulsate");
		$('html,body').animate({scrollTop:0}, 'slow'); // retourne en haut de la page
	};
};

function enregistre_selection()
{
	/**
	 * Fonction d'enregistrement du tout premier brouillon avec la sélection actuelle (Bouton "Valider votre sélection")
	 * désactivation des boutons pour ne pas cliquer deux fois dessus
	 */
	listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
	desactiveBouton(listeBouton);
//	$("#Rechercher").attr("Disabled","Disabled");
//	$("#Ajouter_Selection_Hote").attr("Disabled","Disabled");
//	$("#Valider_Selection").attr("Disabled","Disabled");
	$("#messageValidationOK").remove();

	/**
	 *  Traitement de l'onglet Info Généralres
	 */
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
//		listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
		activeBouton(listeBouton);
//		$("#Rechercher").removeAttr("Disabled");
//		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
//		$("#Valider_Selection").removeAttr("Disabled");
		return false;
	};

	/**
	 * appel de la fonction de récupération des infos générales
	 */
	var info_gen = info_generale();
	
	/**
	 *  Traitement de l'onglet Liste des hôtes et services
	 *  
	 *  boucler sur les hôtes sélectionnés afin de générer une liste
	 */

	var hote_liste = new Array();
	var tableau_hote_lig = document.getElementById("T_Liste_Hote").rows; //charge les lignes du tableau
	var NbLigne_Hote = tableau_hote_lig.length; // récupération du nombre d'enregistrement
	
	var jh=0;
	var kh=0;
	for (var i=1;i<NbLigne_Hote;i++)
	{
		var tableau_hote_liste_col = tableau_hote_lig[i].cells; // charge les cellules de chaque ligne dans un tableau
		hote_liste[kh] = tableau_hote_liste_col[3].innerHTML + ';';					// Hôte
		hote_liste[kh] += tableau_hote_liste_col[4].innerHTML + ';';				// Description => ajout le 04-11-14
		hote_liste[kh] += tableau_hote_liste_col[5].innerHTML + ';';				// Adresse IP
		hote_liste[kh] += tableau_hote_liste_col[6].innerHTML + ';';				// Controle Atif ou inactif => ajout le 04-11-14
		hote_liste[kh] += tableau_hote_liste_col[7].innerHTML.substring(1) + ';';	// ID_Hote Centreon dépréfixé du h
		hote_liste[kh] += document.getElementById(i).checked;						// Checked true ou false
		if (document.getElementById(i).checked ==true)
		{
			$('input:checked[id=' + i +']').attr("disabled","disabled"); // verrouille l'hôte inséré pour éviter un ajout en double.
			$('input:checked[id=' + i +']').removeAttr("checked"); // décoche l'hôte inséré pour éviter un ajout en double.
		};

		kh++;
	};
		hote_liste = hote_liste.join('$');
		
	/**
	 *  boucler sur les services sélectionnés afin de générer une liste
	 */
	var nbcheck_service = $('input:checked[name=selection_service]').size(); // récupère le nombre d'hôte désactivés
	if (nbcheck_service > 0)
	{
		var service_selec= new Array();
		var tableau_service_lig = document.getElementById("T_Liste_Service").rows; //charge les lignes du tableau
		var NbLigne_Service = tableau_service_lig.length; // récupération du nombre d'enregistrement
		var js=0;
		for (var i=1;i<NbLigne_Service;i++)
		{
			$('input:checked[id=s' + i +']').each(function() // on boucle sur chaque objet sélectionné
			{
				var tableau_service_col = tableau_service_lig[i].cells;
				service_selec[js] = tableau_service_col[2].innerHTML + ';';					// Nom du service
				service_selec[js] += tableau_service_col[3].innerHTML + ';';				// Fréquence de controle
				service_selec[js] += tableau_service_col[4].innerHTML + ';';				// Plage Horaire
				service_selec[js] += tableau_service_col[5].innerHTML + ';';				// Controle actif ou inactif
				service_selec[js] += tableau_service_col[6].innerHTML.substring(1) + ';';	// ID_Service Centreon (dépréfixé du s)
				service_selec[js] += tableau_service_col[7].innerHTML.substring(1) + ';';	// ID_Host Centreon (dépréfixé du h)
				service_selec[js] += document.getElementById('s'+i).checked;						// Checked true ou false
				$(this).removeAttr("checked"); // décoche le service inséré pour éviter un ajout en double.
				$(this).attr("disabled","disabled"); // verrouille le service inséré pour éviter un ajout en double
				js++;
			}); 
		};
		service_selec = service_selec.join('$');
	} else
	{
		/**
		 * si aucune sélection, initialise la variable à vide pour ne pas planter l'intégration en base
		 */
		service_selec = "";
	};

	/**
	 *  boucler sur les plages sélectionnés afin de générer une liste
	 */

	var plage_selec= new Array();
	var plage_liste = new Array();
	var tableau_plage_lig = document.getElementById("T_Liste_Plage").rows; //charge les lignes du tableau
	var NbLigne_Plage = tableau_plage_lig.length; // récupération du nombre d'enregistrement
	
	var jp=0;
	var kp=0;
	for (var i=1;i<NbLigne_Plage;i++)
	{
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
	};
	plage_liste = plage_liste.join('$');

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
				collecte_liste_hote(); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
				collecte_liste_plage(); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
				collecte_liste_service(); // fonction de remplissage de liste Param Service => fonction_remplissage.js
				$("#img_loading").remove();
				$("#p_loading").remove();
//				listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
				activeBouton(listeBouton);
				callback(xhr.responseText); // C'est bon \o/
			} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
				$("#img_loading").remove();
				$("#p_loading").remove();
//				listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
				activeBouton(listeBouton);
//				$("#Rechercher").removeAttr("Disabled");
//				$("#Ajouter_Selection_Hote").removeAttr("Disabled");
//				$("#Valider_Selection").removeAttr("Disabled");
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
				$("#tabs-2").append('<img id="img_loading" src="images/chargement.gif" alt="chargement des données..."/> ');
				$("#tabs-2").append('<p id="p_loading">Veuillez patienter pendant le chargement des données...</p>');
			};
		};
		
		var sinfo_gen = encodeURIComponent(info_gen);
		var shote_liste = encodeURIComponent(hote_liste);
		var sservice_selec = encodeURIComponent(service_selec);
		var splage_liste = encodeURIComponent(plage_liste);
		
		xhr.open("POST", "insertion_selection.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("info_gen="+sinfo_gen+"&hote_liste="+shote_liste+"&service_selec="+sservice_selec+"&plage_liste="+splage_liste+""); 
	};
	function affiche_resultat(resultat){
		$("#lien_tabs3").removeAttr("style");
//		listeBouton="Rechercher;Ajouter_Selection_Hote;Valider_Selection";
		activeBouton(listeBouton);
//		$("#Rechercher").removeAttr("Disabled");
//		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
//		$("#Valider_Selection").removeAttr("Disabled");
		$('html,body').animate({scrollTop:0},'slow'); // retourne en haut de la page après l'enregistrement
		$("#messageValidation").append('<p id="messageValidationOK" style="background-color:green">Chargement terminé!<br>Vous pouvez passer au paramétrage des hôtes et services sur l\'onglet paramétrage.</p>');
		$("#lien_tabs3").effect("pulsate");
	}; 
}; 

function PreEnregistrer_fieldset_hote(champ)
{
	timer_enregistrement();
	controle_doublon();
	if (Doublon != "Oui")
	{
		
		var parent=$(champ).parent().parent().attr("id"); // récupèrele fieldset parent hote
		var hote_bouton_id = $(champ).attr("id"); // récupère l'id du bouton
		var Nom_Hote = $("fieldset#"+parent+ " input[id*='Nom_Hote']").val();
		var IP_Hote = $("fieldset#"+parent+ " input[id*='IP_Hote']").val();
		var MessageConfirmation = "Vous allez pré-enregistrer l'hôte [" + Nom_Hote + "].\n Il sera alors disponible pour le paramétrage des services mais vous ne pourrez plus changer son nom.\n Etes-vous sûr?";
		if (confirm(MessageConfirmation))
		{
			var liste_hote = "";
			/**
			 *  enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
			 */
			$(".hote" + parent.substring(4) + "").each(function()
			{
				/**
				 *  gestion des caractères spéciaux ! $ et | dans les champs.
				 */
				var Valeur_Champ =  $(this).val();
				Gestion_caractere_speciaux(Valeur_Champ);
				if ($(this).val() != "Autre" && $(this).val() != "Vide")
				{
					liste_hote += "|" + Valeur_Champ;
				};
			});
			liste_hote += "$";
			liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			/**
			 *  transmettre les données au serveur pour MAJ des infos.
			 */
			
			function PreEnregistre_Hote(callback)
			{
				var xhr = getXMLHttpRequest(); //création de l'instance XHR
				var loading=false;
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						$("#img_loading").remove();
						$("#p_loading").remove();
						listeBouton="Enregistrer_Brouillon;Valider_Demande";
						activeBouton(listeBouton);
						callback(xhr.responseText); // C'est bon \o/
					} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
						$("#img_loading").remove();
						$("#p_loading").remove();
						//$("#"+hote_bouton_id+"").removeAttr("Disabled"); // réactive le bouton
						listeBouton="Enregistrer_Brouillon;Valider_Demande;" + hote_bouton_id;
						activeBouton(listeBouton);
						gestion_erreur(xhr);
					} else if (loading == false){
						loading=true;
						$("fieldset#"+parent+ "").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..." sssssstyle="vertical-align:middle;isplay:inline;"/> ');
						$("fieldset#"+parent+ "").append('<p id="p_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
					};
				};
				var sliste_hote = encodeURIComponent(liste_hote);
				xhr.open("POST", "PreEnregistrement_Hote.php", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
				xhr.send("liste_hote="+sliste_hote+""); 
			};
			function PreEnregistrement_termine(ID_Hote) //récupère la valeur retournée par le script php PreEnregistrement_Hote.php
			{
				$("select[id*='Hote_Service']").each(function()
				{
					$(this).append('<option value="'+ ID_Hote +'">'+ Nom_Hote + ' - ' + IP_Hote + '</option>'); // on ajoute à la liste déroulante le nouvel hôte
				});
				var id_input_hote=hote_bouton_id.substring(14);
				$("#Nom"+id_input_hote+"").attr("ReadOnly","ReadOnly"); // passe le champ Nom_Hote en lecture seule
				$("#img_Nom"+id_input_hote+"").attr("src","images/img_ver.png"); // passe le champ im_Nom_Hote en Verrouillé
				listeBouton=hote_bouton_id;
				desactiveBouton(listeBouton);
			}; 
			PreEnregistre_Hote(PreEnregistrement_termine);
		};
	};
};

function PreEnregistrer_fieldset_plage(champ)
{
	timer_enregistrement();
	var parent=$(champ).parent().parent().parent().attr("id"); // récupèrele fieldset parent plage
	var plage_bouton_id = $(champ).attr("id"); // récupère l'id du bouton
	var Nom_Plage = $("input#Nom_"+parent).val();
	var MessageConfirmation = "Vous allez pré-enregistrer la plage [" + Nom_Plage + "].\n Elle sera alors disponible pour le paramétrage des services mais vous ne pourrez plus changer son nom.\n Etes-vous sûr?";
	if (confirm(MessageConfirmation))
	{
		var liste_plage = "";
		/**
		 *  enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
		 */
		$(".plage" + parent.substring(5) + "").each(function()
		{
			/**
			 *  gestion des caractères spéciaux ! $ et | dans les champs.
			 */
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).val() != "Autre" && $(this).val() != "Vide")
			{
				liste_plage += "|" + Valeur_Champ;
			};
		});
		liste_plage += "$";
		liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		
		/**
		 *  transmettre les données au serveur pour MAJ des infos.
		 */
		
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
					//$("#"+plage_bouton_id+"").removeAttr("Disabled"); // ré-active le bouton
					listeBouton=plage_bouton_id;
					activeBouton(listeBouton);
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
			listeBouton=plage_bouton_id;
			desactiveBouton(listeBouton);
		}; 
		PreEnregistre_Plage(PreEnregistrement_plage_termine);
	};
};

function Valider_Demande()
{
	/**
	 *  Fonction Validation Demande
	 */

	Service_Vide=false;
	Hote_Vide=false;
	Plage_Vide=false;
	Modele_Vide=false;

	listeBouton="Enregistrer_Brouillon;Valider_Demande";
	desactiveBouton(listeBouton);
//	$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
//	$("#Valider_Demande").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...

	var NbFieldset_hote = $("fieldset.hote").length;
	var NbFieldset_service = $("fieldset.service").length;
	var NbFieldset_plage = $("fieldset.plage").length;
	if (NbFieldset_hote == 0 && NbFieldset_service == 0 && NbFieldset_plage == 0){
		alert("Votre demande ne comporte aucun hôte ni service ni période temporelle à traiter. L'enregistrement est arrêté.\nVous devez renseigner au moins un hôte, un service ou une plage horaire pour que la demande soit valide.");
//		listeBouton="Enregistrer_Brouillon;Valider_Demande";
		activeBouton(listeBouton);
//		$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//		$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
		return false;
	};
	controle_doublon();
	/**
	 * Controle des hôtes non pré-enregistrés
	 * pour chaque fieldset hote on vérifie si un bouton "Préenregistré" est présent qu'il est désactivé
	 * S'il ne l'est pas, on stop la procédure et on demande à l'utilisateur de préenregistrer les hôtes.
	 */
	controle_preenregistrement_hote();
	if ((Doublon != "Oui") && (PreEnregistre != false)) 
	{
	
		var MessageConfirmation = "Voulez-vous valider votre demande?\nUne fois la demande validée, vous ne pourrez plus la modifier.";
		if (confirm(MessageConfirmation)) 
		{
			var Verif_Param = true;
			var Liste = "";
	
			/**
			 *  vérification de la validité des champs obligatoires
			 */
			$("#tabs-3 .verif").each(function(){
				if ($(this).attr("alt") != "correct"){
					Verif_Param = false;
					var ID = $(this).attr("id").substring(4);
					Liste += " - " + $("#"+ ID).attr("name") + '\n';
				};
			});
			if (Verif_Param != true) // Tous les champ ne sont pas vérifiés
			{
				var message="STOP! Tous les champs de l'onglet Paramétrage ne sont pas validés!\nVeuillez vérifier les champs suivants:\n";
				alert(message+Liste);
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return false;
			};

			/**
			 * appel de la fonction de récupération des infos générales
			 */
			var info_gen = info_generale();
	
			/**
			 *  constitution de la chaine hote
			 */
			var liste_hote="";
			$("fieldset[class='hote']").each(function()
			{
				var class_hote = $(this).attr("id").toLowerCase();
				$("."+class_hote+"").each(function()
				{
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						liste_hote += "|" + Valeur_Champ;
					};
				});
				liste_hote += "$";
			});
			liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_hote = liste_hote.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
			/**
			 *  constitution de la chaine plage
			 */
			var liste_plage="";
			$("fieldset[class='plage']").each(function()
			{
				var class_plage = $(this).attr("id").toLowerCase();
				$("."+class_plage+"").each(function()
				{
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						liste_plage += "|" + Valeur_Champ;
					};
				});
				liste_plage += "$";
			});
			liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_plage = liste_plage.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
			/**
			 *  constitution de la chaine service
			 */
			var liste_service="";
			$("fieldset[class='service']").each(function()
			{
				/**
				 *  création de la chaine service hors arguments
				 */
				var class_service = $(this).attr("id").toLowerCase();
				$("."+class_service+"").each(function()
				{
					if (($(this).attr("id") == "Nom_Service" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez donner un nom pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Service_Vide=true;
						return Service_Vide;
					};
					if (($(this).attr("id") == "Hote_Service" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner un hôte pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Hote_Vide=true;
						return Hote_Vide;
					};
					if (($(this).attr("id") == "Service_Plage" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner une plage de contrôle pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Plage_Vide=true;
						return Plage_Vide;
					};
					if (($(this).attr("id") == "Service_Modele" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner un modèle pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Modele_Vide=true;
						return Modele_Vide;
					};

					/**
					 *  gestion des caractères spéciaux !, $, | et \ dans les champs.
					 */
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
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ_avant =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ_avant);
					liste_service_Arg += "!" + Valeur_Champ;
				});
	
				/**
				 *  correction des seuils disque en occupé si controle via SNMP (Disque!warning!critique)
				 */
				var modele=$("#Service_Modele" + class_service.substring(7) + " option:selected").text();
				
				if (modele.indexOf(": Disque Linux") >=0)
				{
					//alert("c'est un disque");
					correction_seuils_disque(liste_service_Arg);
				};
				
				liste_service += "|" + liste_service_Arg.substring(1) + "$"; // on enlève le premier | des arguments et on ajoute un $ à la fin
			});
			liste_service = liste_service.substring(1,liste_service.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_service = liste_service.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
				
			/**
			 *  transmettre les données au serveur pour MAJ des infos.
			 * Exemple de chaine	
			 * DISK_/	#BEEWARE	#D-S 0h-Minuit	#30 min / 3 min	#actif	#	#	#Modifier	#79!/fghj!85!90
			 */
			
			function MAJ_infos_Sondes(callback)
			{
				var xhr = getXMLHttpRequest(); //création de l'instance XHR
				var loading=false;
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						$("#msg_loading").remove();
						$("#img_loading").remove();
//						listeBouton="Enregistrer_Brouillon;Valider_Demande";
						activeBouton(listeBouton);
//						$("#Enregistrer_Brouillon").removeAttr("Disabled");
//						$("#Valider_Demande").removeAttr("Disabled");
						callback(xhr.responseText); // C'est bon \o/
					} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
						$("#msg_loading").remove();
						$("#img_loading").remove();
//						listeBouton="Enregistrer_Brouillon;Valider_Demande";
						activeBouton(listeBouton);
//						$("#Enregistrer_Brouillon").removeAttr("Disabled");
//						$("#Valider_Demande").removeAttr("Disabled");
						gestion_erreur(xhr);
					} else if (loading == false){
						loading=true;
						$("#tabs-3").append('<p id="msg_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
						$("#tabs-3").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter..."/> ');
					};
				};
				var sinfo_gen = encodeURIComponent(info_gen);
				var sliste_plage = encodeURIComponent(liste_plage);
				var sliste_hote = encodeURIComponent(liste_hote);
				var sliste_service = encodeURIComponent(liste_service);
				
				xhr.open("POST", "enregistrement_Demande.php", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
				xhr.send("info_gen="+sinfo_gen+"&liste_plage="+sliste_plage+"&liste_hote="+sliste_hote+"&liste_service="+sliste_service+""); 
			};
			function enregistrement_termine(resultat)
			{
				var ref_demande = $("#ref_demande").val();
				alert("Enregistrement de la demande [" + ref_demande + "] terminé!\nVous pouvez consulter l'état des demandes via le menu <Lister les demandes>.\nVous recevrez une confirmation d'enregistrement d'ici quelques minutes par l\'intermédiaire de SUSI avec le numéro de ticket correspondant.");
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled");
//				$("#Valider_Demande").removeAttr("Disabled");
				window.location.replace('index.php'); // recharge la page d'accueil
			}; 
			if ((Hote_Vide == false) && (Plage_Vide == false) && (Modele_Vide == false)) //si hote, plage et modele sont rempli on met à jour
			{
				MAJ_infos_Sondes(enregistrement_termine);
			} else
			{
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled");
//				$("#Valider_Demande").removeAttr("Disabled");
				return false;
			};
		} else
		{
//			listeBouton="Enregistrer_Brouillon;Valider_Demande";
			activeBouton(listeBouton);
//			$("#Enregistrer_Brouillon").removeAttr("Disabled");
//			$("#Valider_Demande").removeAttr("Disabled");
		};
	};
};

function Enregistrer_Brouillon(Bouton)
{
	/**
	 *  Fonction Enregistre Brouillon
	 */
	if ($("#lien_tabs3").css("Visibility") == "visible")
	{

	Service_Vide=false;
	Hote_Vide=false;
	Plage_Vide=false;
	Modele_Vide=false;

	listeBouton="Enregistrer_Brouillon;Valider_Demande";
	desactiveBouton(listeBouton);
//	$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
//	$("#Valider_Demande").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
	controle_doublon();
	/**
	 * Controle des hôtes non pré-enregistrés
	 * pour chaque fieldset hote on vérifie, si un bouton "Préenregistré" est présent, qu'il est désactivé
	 * S'il ne l'est pas, on stop la procédure et on demande à l'utilisateur de préenregistrer les hôtes.
	 */
	controle_preenregistrement_hote();
	if ((Doublon != "Oui") && (PreEnregistre != false)) 
	{
		function Traitement_Enregistre()
		{
			var Liste = "";
			/**
			 * appel de la fonction de récupération des infos générales
			 */
			var info_gen = info_generale();
		
			/**
			 *  constitution de la chaine hote
			 */
			var liste_hote="";
			$("fieldset[class='hote']").each(function()
			{
				var class_hote = $(this).attr("id").toLowerCase();
				$("."+class_hote+"").each(function()
				{
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						liste_hote += "|" + Valeur_Champ;
					};
				});
				liste_hote += "$";
			});
			liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_hote = liste_hote.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
			/**
			 *  constitution de la chaine plage
			 */
			var liste_plage="";
			$("fieldset[class='plage']").each(function()
			{
				var class_plage = $(this).attr("id").toLowerCase();
				$("."+class_plage+"").each(function()
				{
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ);
					if ($(this).val() != "Autre" && $(this).val() != "Vide")
					{
						liste_plage += "|" + Valeur_Champ;
					};
				});
				liste_plage += "$";
			});
			liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_plage = liste_plage.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
			/**
			 *  constitution de la chaine service
			 */
			var liste_service="";
			$("fieldset[class='service']").each(function()
			{
				/**
				 *  création de la chaine service hors arguments
				 */
				var class_service = $(this).attr("id").toLowerCase();
				$("."+class_service+"").each(function()
				{
					if (($(this).attr("id") == "Nom_Service" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez donner un nom pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Service_Vide=true;
						return Service_Vide;
					};
					if (($(this).attr("id") == "Hote_Service" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner un hôte pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Hote_Vide=true;
						return Hote_Vide;
					};
					if (($(this).attr("id") == "Service_Plage" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner une plage de contrôle pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Plage_Vide=true;
						return Plage_Vide;
					};
					if (($(this).attr("id") == "Service_Modele" + class_service.substring(7)) && ($(this).val() == ""))
					{
						$("#bip").append('<p class="attention" id="bip_retour">Veuillez sélectionner un modèle pour le service ' + class_service.substring(7) + ' avant de continuer.</p>');
						afficheMessage(15,"bip_retour");
						Modele_Vide=true;
						return Modele_Vide;
					};
					/**
					 *  gestion des caractères spéciaux !, $, | et \ dans les champs.
					 */
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
					/**
					 *  gestion des caractères spéciaux ! $ et | dans les champs.
					 */
					var Valeur_Champ_avant =  $(this).val();
					Gestion_caractere_speciaux(Valeur_Champ_avant);
					liste_service_Arg += "!" + Valeur_Champ;
				});

				/**
				 *  correction des seuils disque en occupé si controle via SNMP (Disque!warning!critique)
				 */
				var modele=$("#Service_Modele" + class_service.substring(7) + " option:selected").text();
				
				if (modele.indexOf(": Disque Linux") >=0)
				{
					//alert("c'est un disque");
					correction_seuils_disque(liste_service_Arg);
				};
					
				liste_service += "|" + liste_service_Arg.substring(1) + "$"; // on enlève le premier | des arguments et on ajoute un $ à la fin
			});
			liste_service = liste_service.substring(1,liste_service.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
			liste_service = liste_service.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
			/**
			 *  transmettre les données au serveur pour MAJ des infos.
			 * Exemple de chaine	
			 * DISK_/	#BEEWARE	#D-S 0h-Minuit	#30 min / 3 min	#actif	#	#	#Modifier	#79!/fghj!85!90
			 */

			function MAJ_Brouillon(callback)
			{
				var xhr = getXMLHttpRequest(); //création de l'instance XHR
				var loading=false;
				xhr.onreadystatechange = function()
				{
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
					{
						$("#msg_loading").remove();
						$("#img_loading").remove();
//						listeBouton="Enregistrer_Brouillon;Valider_Demande";
						activeBouton(listeBouton);
//						$("#Enregistrer_Brouillon").removeAttr("Disabled");
//						$("#Valider_Demande").removeAttr("Disabled");
						callback(xhr.responseText); // C'est bon \o/
					} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
						$("#msg_loading").remove();
						$("#img_loading").remove();
//						listeBouton="Enregistrer_Brouillon;Valider_Demande";
						activeBouton(listeBouton);
//						$("#Enregistrer_Brouillon").removeAttr("Disabled");
//						$("#Valider_Demande").removeAttr("Disabled");
						gestion_erreur(xhr);
					} else if (loading == false)
					{
						loading=true;
						$("#tabs-3").append('<p id="msg_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
						$("#tabs-3").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter..."/> ');
					};
				};
				var sinfo_gen = encodeURIComponent(info_gen);
				var sliste_plage = encodeURIComponent(liste_plage);
				var sliste_hote = encodeURIComponent(liste_hote);
				var sliste_service = encodeURIComponent(liste_service);
			
				xhr.open("POST", "enregistrement_Brouillon.php", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
				xhr.send("info_gen="+sinfo_gen+"&liste_plage="+sliste_plage+"&liste_hote="+sliste_hote+"&liste_service="+sliste_service+""); 
			};
			function enregistrement_brouillon(resultat)
			{
				$("#bip").append('<p id="bip_retour">Brouillon enregistré!<br> <button onclick="goToMenu()">Cliquez sur ce bouton</button> pour laisser votre demande en l\'état et revenir immédiatement à l\'accueil sinon continuer de tavailler normalement.<br>Ce message disparaitra automatiquement dans 10 secondes.</p>');
				afficheMessage(10,"bip_retour");
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled");
//				$("#Valider_Demande").removeAttr("Disabled");
			}; 
			if ((Hote_Vide == false) && (Plage_Vide == false) && (Modele_Vide == false)) //si hote, plage et modele sont rempli on met à jour
			{
				MAJ_Brouillon(enregistrement_brouillon);
			} else
			{
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled");
//				$("#Valider_Demande").removeAttr("Disabled");
				return false;
			};
			};
			if (Bouton == true)
			{
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				desactiveBouton(listeBouton);
//				$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
//				$("#Valider_Demande").attr("Disabled","Disabled"); // Désactivation du bouton Valider Votre Demande pour éviter tout double clic...
				Traitement_Enregistre(); // on enregistre les données
			}else
			{
				Traitement_Enregistre(); // on enregistre les données	
			};
		};
	} else
	{
//		listeBouton="Enregistrer_Brouillon;Valider_Demande";
		activeBouton(listeBouton);
//		$("#Enregistrer_Brouillon").removeAttr("Disabled");
//		$("#Valider_Demande").removeAttr("Disabled");
		return false;
	};
};

function afficheMessage(delai,maDiv)
{
	/**
	 * Fonctions de gestion des messages intégrés
	 * 	start lance le compteur
	 *	decompte.... décompte le temps
	 *	action supprime le message une fois le délai passé
	 */

	var counter = delai;
	var intervalId = null;
	var maDiv = maDiv;
	function action()
	{
		clearInterval(intervalId);
		$("#" + maDiv + "").remove();
	};
	function decompte()
	{
		counter--;
	};
	function start()
	{
		var counter=delai
		intervalId = setInterval(decompte, 1000);
		setTimeout(action, counter * 1000);
	};
	start();
};

function enregistre_Etat_Demande(champ,ID)
{ // 
	/**
	 * fonction d'enregistrement de l'état du paramétrage pour le fieldset hote, service ou plage en cours
	 *  => passe la demande globale au statut "En cours" si ce n'est pas le cas.
	 * ID correspond à l'ID_Hote, l'ID_Service ou l'ID_periode selon le cas
	 */
	var parent=$(champ).parent().parent().parent().attr("id"); // récupèrele fieldset parent DEC_hote contenant l'ID_Demande
	var fieldset_parent=$(champ).parent().attr("id");
	var ID_Hote = "";
	var ID_Service = "";
	var ID_Plage = "";
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
		var motif_annulation="";
	    while ( motif_annulation == "") // tant que l'utilisateur valide une chaine vide on redemande la saisie du motif
	    {
	   	motif_annulation=prompt("Motif de l'annulation (obligatoire):","doublon");
	    };
	   	if (motif_annulation === null){// si l'utilisateur clique sur Annuler, on sort simplement de la fonction
	   		return; 
	   	}
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_annu");
	} else if (Etat_Param == "Supprimer")
	{
		$("Select#"+champ.id.substring(12)).removeAttr("class");
		$("Select#"+champ.id.substring(12)).attr("class","etat_dem_supp");
	};
	if (parent.indexOf('hote')>0) // si c'est un hote
	{
		var ID_Demande = parent.substring(8);
		ID_Hote = ID;
	} else if (parent.indexOf('service')>0) // si c'est un service
	{
		var ID_Demande = parent.substring(11);
		ID_Service = ID;
	} else if (parent.indexOf('plage')>0) // si c'est une plage
	{
		var ID_Demande = parent.substring(9);
		ID_Plage = ID;
	};

	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			var Etat_dem=$("#Liste_DEC_Enregistrer_Etat"+ID_Demande).val();
			if (Etat_Param == "En cours" && Etat_dem == "A Traiter")
			{
				//window.location.reload(); // rechargement de la page pour afficher le statut "en cours"
				$(".statut_demande" + ID_Demande).html(Etat_Param);
				$(".statut_demande" + ID_Demande).attr("class", "ok");
				$("#Liste_DEC_Enregistrer_Etat" + ID_Demande).empty(); // vide la liste déroulante
				requete_maj_list_etat(ID_Demande,Etat_Param);
//				$("#Liste_DEC_Enregistrer_Etat" + ID_Demande).val(Etat_Param);
//				$("#Liste_DEC_Enregistrer_Etat" + ID_Demande).find("option[value="+Etat_Param+"]").attr("Selected","Selected");
			};
			
			var bip_id=fieldset_parent + "bip_enregistre";
			$("#" + fieldset_parent + "").append('<span id="' + bip_id + '">etat enregistré!</span>');
			afficheMessage(3,bip_id);

		} else if(xhr.readyState == 4 && xhr.status != 200) 
		{ 
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
	var eAnnulation = encodeURIComponent(motif_annulation);
	
	xhr.open("POST", "MAJ_Etat_Parametrage.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("ID_Demande="+eID_Demande+"&ID_Hote="+eID_Hote+"&ID_Service="+eID_Service+"&ID_Plage="+eID_Plage+"&Etat_Param="+eEtat_Param+"&Annulation="+eAnnulation+""); 

};

function requete_maj_list_etat(ID_Demande,Etat_Param)
{
	var xhr_r = getXMLHttpRequest(); //création de l'instance XHR
	xhr_r.onreadystatechange = function()
	{
		if (xhr_r.readyState == 4 && (xhr_r.status == 200 || xhr_r.status == 0))
		{
			$("#Liste_DEC_Enregistrer_Etat" + ID_Demande).append(xhr_r.responseText); //met à jour la liste déroulante avec les nouvelles valeurs
		} else if(xhr_r.readyState == 4 && xhr_r.status != 200) 
		{ 
			gestion_erreur(xhr_r);
		};
	};
	xhr_r.open("POST", "requete_maj_etat_dem.php", true);
	xhr_r.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_r.send("Etat_Param="+Etat_Param+""); 
};

function DEC_enregistre_Etat_Demande(champ,ID_Demande)
{ 
	/**
	 *  fonction d'enregistrement de l'état du paramétrage pour l'ensemble de la demande en cours
	 *   => Tous les hôtes, services et plages seront forcés dans l'état choisi ainsi que la demande.
	 */
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
		if (Etat_Param == "Annulé")
		{
			var motif_annulation="";
		    while ( motif_annulation == "") // tant que l'utilisateur valide une chaine vide on redemande la saisie du motif
		    {
		   	motif_annulation=prompt("Motif de l'annulation (obligatoire):","doublon");
		    };
		   	if (motif_annulation === null){// si l'utilisateur clique sur Annuler, on sort simplement de la fonction
		   		return; 
		   	}
		};

		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
				/**
				 * Désactivation demande rechargement => rechargement obligatoire avec l'activation des mails auto.
				 */
				// version 8.11	
				window.location.reload();
			} else if(xhr.readyState == 4 && xhr.status != 200) 
			{ 
				gestion_erreur(xhr);
			};
		};

		ID_Demande = encodeURIComponent(ID_Demande);
		Etat_Param = encodeURIComponent(Etat_Param);
		Annulation = encodeURIComponent(motif_annulation);
		
		xhr.open("POST", "MAJ_Etat_Parametrage_Demande.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("ID_Demande="+ID_Demande+"&Etat_Param="+Etat_Param+"&Annulation="+Annulation+""); 
	};
};

function DEC_Supprimer_Demande(ID_Demande)
{ 
	/**
	 *  fonction spécifique de suppression de demande par l'utilisateur
	 *   => Tous les hôtes, services et plages seront supprimés ainsi que la demande.
	 */
	var Etat_Param = "Supprimer";
	var MessageConfirmation = "Vous allez définitivement supprimer la demande n°" + ID_Demande + " ainsi que tous les hôtes, plages et services associés. Etes-vous sûr?";
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
				window.location.reload();
			} else if(xhr.readyState == 4 && xhr.status != 200) 
			{ 
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
	listeBouton="Enregistre_Notif_BAM";
	desactiveBouton(listeBouton);
//	$("#Enregistre_Notif_BAM").attr("Disabled","Disabled");
	var Verif_Info = true;
	$("#field_config_notification .verif").each(function(){
		if ($(this).attr("alt") != "correct"){
			Verif_Info = false;
		};
	});

	if (Verif_Info != true) // l'onglet info générale doit contenir 7 valeurs impérativement
	{
		var message="STOP! Tous les champs ne sont pas valides!";
//		listeBouton="Enregistre_Notif_BAM";
		activeBouton(listeBouton);
//		$("#Enregistre_Notif_BAM").removeAttr("Disabled"); // réactive le bouton
		alert(message);
		return false;
	};

	var liste_conf = "";
	/**
	 * enregistrer les infos saisies jusqu'à maintenant sans vérification pour l'instant
	 */
	$(".gb_config").each(function()
	{
		/**
		 *  gestion des caractères spéciaux ! $ et | dans les champs.
		 */
		var Valeur_Champ =  $(this).val();
		/**
		 * Gestion_caractere_speciaux(Valeur_Champ);
		 */
			if ($(this).attr("type")=="checkbox")
			{
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
	$("select[id='am_associe'] option").each(function() {
	select_am += $(this).val().substring(0,$(this).val().indexOf("_")) + '$';
	});
	/**
	 * enlève le dernier ; de la chaine select_am
	 */
	select_am = select_am.substring(0,select_am.length-1);
	liste_conf += "|" + select_am;
	liste_conf = liste_conf.substring(1); // enlève le premier "|"
	/**
	 *  transmettre les données au serveur pour MAJ des infos.
	 */
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	var loading=false;
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			$("#img_loading").remove();
			$("#p_loading").remove();
//			listeBouton="Enregistre_Notif_BAM";
			activeBouton(listeBouton);
			
			window.location.replace("./administration.php#tabs-3");
			window.location.reload();
		} else if(xhr.readyState == 4 && xhr.status != 200) { // En cas d'erreur !
			$("#img_loading").remove();
			$("#p_loading").remove();
//			listeBouton="Enregistre_Notif_BAM";
			activeBouton(listeBouton);
			
			gestion_erreur(xhr);
		} else if (loading == false){
			loading=true;
			$("#field_config_notification").append('<img id="img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant l\'enregistrement des informations..."/> ');
			$("#field_config_notification").append('<p id="p_loading">Veuillez patienter pendant l\'enregistrement des informations...</p>');
		};
	};
	var sliste_conf = encodeURIComponent(liste_conf);
	xhr.open("POST", "BAM_enregistre_conf.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("liste_conf="+sliste_conf+""); 
};

function set_focus_bouton(ID_bouton)
{ 
	/**
	 *  fonction pour positionner le focus sur le bouton "Forcer"
	 */ 
	var Focus_ID_Bouton = ID_bouton;
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
	/**
	 *  remplacement de tous les antislash par des slash
	 */
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
		str = str.replace(/\$/g,"_DOLLAR_");
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
	var reg1=new RegExp("[\[]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\[/g,"_CO_");
	};
	var reg1=new RegExp("[\\]]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\]/g,"_CF_");
	};
	var reg1=new RegExp("[%5D]","g");
	if (str.match(reg1))
	{
		str = str.replace(/%5D/g,"_CF_");
	};
	var reg1=new RegExp("[\{]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\{/g,"_AO_");
	};
	var reg1=new RegExp("[\}]","g");
	if (str.match(reg1))
	{
		str = str.replace(/\}/g,"_AF_");
	};
	var str2 = "";
	str2 = encodeURI(str);
	Valeur_Champ = str2;
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
			/**
			 *  gestion des caractères spéciaux ! $ et | dans les champs.
			 */
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			if ($(this).attr("id") == "client_new")
			{
				liste_valeur[i] = "NEW_" + Valeur_Champ;
			} else
			{
				liste_valeur[i] = Valeur_Champ;
			};
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
	return info;
};

function controle_doublon()
{
	listeBouton="Enregistrer_Brouillon;Valider_Demande";
	/**
	 *  Vérification des doublons d'hôtes et services et plages
	 */
	/**
	 *  constitution de la chaine hote
	 */
	var NbFieldset_Hote = $("fieldset.hote").length;
	var j = 0;
	var liste_nom_hote="";
	for (var i=1;i<=NbFieldset_Hote;i++)
	{
		/**
		 *  gestion des caractères spéciaux ! $ et | dans les champs.
		 */
		var Valeur_Champ =  $("#Nom_Hote" + i + "").val();
		/**
		 *  Si trou dans la liste on passe au suivant
		 */
		if (typeof(Valeur_Champ) != 'undefined')
		{
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			var Valeur_Champ_IP =  $("#IP_Hote" + i + "").val();
			Gestion_caractere_speciaux(Valeur_Champ_IP);
			Valeur += " " + Valeur_Champ_IP;
			liste_nom_hote += "|" + Valeur;
		};
	};
	liste_nom_hote = liste_nom_hote.substring(1);
	var T_liste_nom_hote = liste_nom_hote.split("|");
	/**
	 *  boucle sur chaque enregistrement de la liste pour trouver les doublons.
	 */
	for (var i=0;i<T_liste_nom_hote.length;i++)
	{
		for (var j=0;j<i;j++)
		{
			if (T_liste_nom_hote[i] == T_liste_nom_hote[j])
			{
				alert("ATTENTION: L'hôte ["+T_liste_nom_hote[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon="Oui";
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return Doublon; // on arrête l'enregistrement du brouillon.
			};
		};
	};
	/**
	 *  constitution de la chaine plage
	 */
	var NbFieldset_Plage = $("fieldset.plage").length;
	var j = 0;
	var liste_nom_plage="";
	for (var i=1;i<=NbFieldset_Plage;i++)
	{
		/**
		 *  gestion des caractères spéciaux ! $ et | dans les champs.
		 */
		var Valeur_Champ =  $("#Nom_Plage" + i + "").val();
		/**
		 *  Si trou dans la liste on passe au suivant
		 */
		if (typeof(Valeur_Champ) != 'undefined')
		{
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			liste_nom_plage += "|" + Valeur;
		};
	};
	liste_nom_plage = liste_nom_plage.substring(1);
	var T_liste_nom_plage = liste_nom_plage.split("|");
	/**
	 *  boucle sur chaque enregistrement de la liste pour trouver les doublons.
	 */
	for (var i=0;i<T_liste_nom_plage.length;i++)
	{
		for (var j=0;j<i;j++)
		{
			if (T_liste_nom_plage[i] == T_liste_nom_plage[j])
			{
				alert("ATTENTION: La plage ["+T_liste_nom_plage[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon = "Oui";

//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
				return Doublon; // on arrête l'enregistrement du brouillon.
			};
		};
	};

	/**
	 *  constitution de la chaine service
	 */
	var NbFieldset_Service = $("fieldset.service").length;
	var j = 0;
	var liste_nom_service="";
	for (var i=1;i<=NbFieldset_Service;i++)
	{
		var Valeur_Champ =  $("#Nom_Service" + i + "").val();
		var Valeur_Champ_Hote =  $("#Hote_Service" + i + " option:selected").text();
		if (typeof(Valeur_Champ) != 'undefined') 
		{
			Gestion_caractere_speciaux(Valeur_Champ);
			var Valeur = Valeur_Champ;
			if ( Valeur == "")
			{
				Valeur= "champ vide"
			};
			Gestion_caractere_speciaux(Valeur_Champ_Hote);
			Valeur += " sur  l'hôte " + Valeur_Champ_Hote;
			liste_nom_service += "|" + Valeur;
		};
	};
	liste_nom_service = liste_nom_service.substring(1);
	var T_liste_nom_service = liste_nom_service.split("|");
	/**
	 *  boucle sur chaque enregistrement de la liste pour trouver les doublons.
	 */
	for (var i=0;i<T_liste_nom_service.length;i++)
	{
		for (var j=0;j<i;j++)
		{
			if (T_liste_nom_service[i] == T_liste_nom_service[j])
			{
				alert("ATTENTION: Le service ["+T_liste_nom_service[i]+"] existe au moins deux fois dans la liste! L'enregistrement est arrêté.\nNotez son nom et corrigez pour enlever les doublons.")
				Doublon = "Oui";
//				listeBouton="Enregistrer_Brouillon;Valider_Demande";
				activeBouton(listeBouton);
//				$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//				$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.

				return Doublon; // on arrête l'enregistrement du brouillon.
			};
		};
	};
	Doublon = "Non";
	return Doublon;
};

function controle_preenregistrement_hote()
{
	/**
	 *  Vérification des boutons pré-enregistrement d'hôte actif ou non
	 */
	/**
	 *  constitution de la chaine hote
	 */
	$("[id*='PreEnregistrer_Hote']").each(function()
	{
		if ($(this).attr("disabled")=="disabled")
		{
			PreEnregistre=true;
		}else
		{
			PreEnregistre=false;
			alert("Attention, au moins un hôte créé n'a pas été pré-enregistré."+PreEnregistre);
			listeBouton="Enregistrer_Brouillon;Valider_Demande";
			activeBouton(listeBouton);
//			$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//			$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.
			return PreEnregistre;
		};
	});
};

function correction_seuils_disque(liste_service_Arg)
{
	var reg1=new RegExp("^![a-zA-Z]{1}:!"); // si la chaine d'argument commence par "!<Lettre de lecteur>:!"
	var reg2=new RegExp("^!/[a-zA-Z/]*!"); // si la chaine d'argument commence par "!/<montage>!"
	if ((liste_service_Arg.match(reg1)) || (liste_service_Arg.match(reg2))) // si la chaine contient l'une ou l'autre des expressions...
	{
		/**
		 *  redécoupe de la chaine pour traitement des seuils
		 */
		var T_T_liste_Arg = liste_service_Arg.substring(1).split("!");
		for (var j=1;j<3;j++)
		{
			if (T_T_liste_Arg[j].substring(T_T_liste_Arg[j].length-3) == "%25") // conversion uniquement si le seuil est en %
			{
				T_T_liste_Arg[j] = T_T_liste_Arg[j].replace(T_T_liste_Arg[j].substring(0,2),100-T_T_liste_Arg[j].substring(0,2));
			};
		};
		liste_service_Arg = "!"+ T_T_liste_Arg[0] + "!" + T_T_liste_Arg[1] + "!"+ T_T_liste_Arg[2];
	};
};

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
		if (Delai_Timer > 900)
		{
			alert("Cela fait plus de 15 minutes que rien n'a été enregistré en base.\nJe vous conseille vivement d'enregistrer votre brouillon maintenant.");
			listeBouton="Enregistrer_Brouillon;Valider_Demande";
			activeBouton(listeBouton);
		};
	}; 
	recuperation_Timer(avertissement_Timer);
};

/**
 * Fonction d'activation des boutons
 * 
 * @listeBouton liste des id des boutons séparés par un point-virgule
 */

function activeBouton(listeBouton)
{
	var T_listeBouton = listeBouton.split(";");
	for (var i=0;i<T_listeBouton.length;i++)
	{
		$("#" + T_listeBouton[i] + "").removeAttr("Disabled"); // réactivation buton.
	}
//	équivalent des lignes ci-dessous
//	$("#Enregistrer_Brouillon").removeAttr("Disabled"); // Réactivation bouton.
//	$("#Valider_Demande").removeAttr("Disabled"); // Réactivation bouton.

};

/**
 * Fonction de désactivation des boutons
 * 
 * @listeBouton liste des id des boutons séparés par un point-virgule
 */

function desactiveBouton(listeBouton)
{
	var T_listeBouton = listeBouton.split(";");
	for (var i=0;i<T_listeBouton.length;i++)
	{
		$("#" + T_listeBouton[i] + "").attr("Disabled","Disabled"); // réactivation buton.
	}
//	équivalent des lignes ci-dessous
//	$("#Enregistrer_Brouillon").attr("Disabled","Disabled"); // désactivation bouton.
//	$("#Valider_Demande").attr("Disabled","Disabled"); // désactivation bouton.

};

function gestion_erreur(xhr)
{
	alert('Une erreur est survenue !\nVeuillez re-essayer. Si le problème persiste, notez le message ci-dessous et contactez l\'administrateur.\nCode:' + xhr.status + '\nNature de l\'erreur: ' + xhr.responseText);	
};