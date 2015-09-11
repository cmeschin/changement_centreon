function collecte_liste_hote(callback) // appelé par fonction_enregistrer.js, appelle rempli_hote
{
		var xhr_h = getXMLHttpRequest(); //création de l'instance XHR
		xhr_h.onreadystatechange = function() {
			if (xhr_h.readyState == 4 && (xhr_h.status == 200 || xhr_h.status == 0)) {
//				collecte_liste_service(rempli_service); // fonction de remplissage de liste Param Service => fonction_remplissage.js
//				collecte_liste_plage(rempli_plage); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
//				callback(xhr.responseText); // affiche le résultat dans la page
				//alert(xhr.responseText); // affiche le résultat dans la page
				$("#param_hote").empty();
				$("#param_hote").append(xhr_h.responseText);
				//collecte_liste_plage(); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
			} else if(xhr_h.readyState == 4 && xhr_h.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_h.status + '\nTexte : ' + xhr_h.statusText);
			};
		};
		xhr_h.open("POST", "remplissage_param_hote.php", true);
		xhr_h.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_h.send(); 
};


function rempli_hote(Liste_Hote)
{
//	Nb_Hote = resultat.substring(0,resultat.indexOf('$'));
//	Liste_Hote = resultat.substring(resultat.indexOf('$')+1);
//	alert("Nb_Hote="+Nb_Hote);
//	alert("liste_hote="+Liste_Hote);
	// découpe de la chaine retournée en deux variables
	if (Liste_Hote != ""){ 	// traitement si au moins 1 Hote est sélectionné
		var T_Hote=Liste_Hote.split("|"); // création du tableau principal
		var NbLigne_T_Hote = T_Hote.length; // nb hote dans le tableau
		var NbFieldset_Hote = $("fieldset.hote").length; // compte le nombre de fielset hote présent dans l'onglet paramétrage
		var Nb_Hote = NbLigne_T_Hote - NbFieldset_Hote;
//		alert("Ajout nouveau fieldset hote => "+ Nb_Hote)
		for (var i = 0;i < Nb_Hote;i++)
		{
			ajoute_fieldset_hote();
		};
		// boucle sur chacun des enregistrements puis redécoupe en un nouveau tableau
		for (var i=0;i<NbLigne_T_Hote;i++)
		{
			var T_Hote_Lig = T_Hote[i].split("!"); // création tableau par ligne
			var j = i+1; // j correspond au numéro de fieldset hote
//			alert("hote="+T_Hote_Lig[0]);

	//			alert("implementation des valeurs");
				// implémentation des valeurs
				// ordre Nom_Hote, IP_Hote, Description, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne
//				alert("Nom_Hote"+j+"="+T_Hote_Lig[0]);
				$("#Nom_Hote"+j).val(T_Hote_Lig[0]);
				$("#IP_Hote"+j).val(T_Hote_Lig[1]);
				$("#Hote_Description"+j).val(T_Hote_Lig[2]);
				$("#Type_Hote"+j).val(T_Hote_Lig[3]);
				$("#Site"+j).val(T_Hote_Lig[4]);
				$("#Type_OS"+j).val(T_Hote_Lig[5]);
				$("#Architecture"+j).val(T_Hote_Lig[6]);
				$("#Langue"+j).val(T_Hote_Lig[7]);
				$("#Fonction"+j).val(T_Hote_Lig[8]);
				$("#Controle_Actif_Hote"+j).val(T_Hote_Lig[9]);
				$("#Hote_Commentaire"+j).val(T_Hote_Lig[10]);
				$("#Consigne_Hote"+j).val(T_Hote_Lig[11]);
		};
	};
};


// Ajouter gestion remplissage service sur modèle ci-dessus
// penser à désactiver le choix de l'hôte lorsque rempli automatiquement OK
// idem sur les autres listes déroulantes avec possibilité de modif sur doubleclic OK
function collecte_liste_service(callback) // appelé par fonction_enregistrer.js, appelle rempli_service
{
		var xhr_s = getXMLHttpRequest(); //création de l'instance XHR
		xhr_s.onreadystatechange = function() {
			if (xhr_s.readyState == 4 && (xhr_s.status == 200 || xhr_s.status == 0)) {
				$("#param_service").empty();
				$("#param_service").append(xhr_s.responseText);
//				callback(xhr.responseText); // affiche le résultat dans la page
//				collecte_liste_plage(rempli_plage); // fonction de remplissage de liste Param Plage => fonction_remplissage.js
			} else if(xhr_s.readyState == 4 && xhr_s.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_s.status + '\nTexte : ' + xhr_s.statusText);
			};
		};
//		alert("Nb_Hote:"+sNb_Hote);
		
		xhr_s.open("POST", "remplissage_param_service.php", true);
		xhr_s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//		xhr.send("Nb_Hote="+sNb_Hote+""); 
		xhr_s.send(); 

};


function rempli_service(Liste_Service)
{
//	Nb_Service = resultat.substring(0,resultat.indexOf('$'));
//	Liste_Service = resultat.substring(resultat.indexOf('$')+1);
//	alert("Nb_Service="+Nb_Service);
//	alert("liste_service="+Liste_Service);
	// découpe de la chaine retournée en deux variables
	if (Liste_Service != ""){ 	// traitement si au moins 1 Service est sélectionné
		var T_Service=Liste_Service.split("|"); // création du tableau principal
		var NbLigne_T_Service = T_Service.length; // nb service dans le tableau
		var NbFieldset_Service = $("fieldset.service").length; // compte le nombre de fielset service présent dans l'onglet paramétrage
		var Nb_Service = NbLigne_T_Service - NbFieldset_Service;
//		alert("Ajout nouveau fieldset service => "+ Nb_Service)
		for (var i = 0;i < Nb_Service;i++)
		{
			ajoute_fieldset_service();
		};
		// boucle sur chacun des enregistrements puis redécoupe en un nouveau tableau
		for (var i=0;i<NbLigne_T_Service;i++)
		{
			var T_Service_Lig = T_Service[i].split("$"); // création tableau par ligne
			var j = i+1; // j correspond au numéro de fieldset service
//			alert("service="+T_Service_Lig[0]);

	//			alert("implementation des valeurs");
				// implémentation des valeurs
				// ordre SELECT Nom_Service, Nom_Hote, Nom_Periode, Frequence, Consigne, Controle_Actif, MS.Modele_Service AS Modele_Service, MS.MS_Libelles AS MS_Libelles, Parametres
//				alert("Nom_Service"+j+"="+T_Service_Lig[0]);
			$("#Nom_Service"+j).val(T_Service_Lig[0]);
			$("#Hote_Service"+j).val(T_Service_Lig[1]);
			$("#Service_Plage"+j).val(T_Service_Lig[2]);
			$("#Frequence_Service"+j).val(T_Service_Lig[3]);
			$("#Service_Consigne"+j).val(T_Service_Lig[4]);
			$("#Service_Actif"+j).val(T_Service_Lig[5]);
			$("#Service_Modele"+j).val(T_Service_Lig[6]);
//			alert("Contenu="+"Service_Modele"+j);
			afficher_argument("Service_Modele"+j); //appelle fonction_liste.js
// remplissage des arguments avec les valeurs du service avec en argument les paramètres soit T_Service_Lig[8]
//			alert("Liste_Argument avant fonction=" + T_Service_Lig[8]);
			rempli_argument("Service_ArgumentService_Modele"+j,T_Service_Lig[8]);
//			alert("fonction afficher_argument passée");
			//Desactivation des champs non modifiables
			$("#Hote_Service"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Hote_Service"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Hote_Service"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Service_Plage"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Service_Plage"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Service_Plage"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Service_Actif"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Service_Actif"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Service_Actif"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Frequence_Service"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Frequence_Service"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Frequence_Service"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Service_Modele"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Service_Modele"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Service_Modele"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK
			
		};
	};
	//alert("terminé");
};



function rempli_argument(Service_Argument_id,Liste_Argument)
{
	//alert("Service_Argument_id="+Service_Argument_id);
	alert("liste_argument="+Liste_Argument);
	if (Liste_Argument != ""){ 	// traitement si au moins 1 ARgument est envoyé
		// Suppression des \ dans la liste des arguments
		//Liste_Argument = Liste_Argument.replace(/\\/g,'');
		//alert("Liste_Argument corrigée=" + Liste_Argument);
		if ((Liste_Argument.indexOf(" ") > 0) && (Liste_Argument.indexOf("!") < 0)){
			//alert("découpage des espaces");
			var T_Argument=Liste_Argument.split(" "); // création du tableau principal avec comme séparateur un espace; permet de gérer les arguments non détaillés dans les modèles de service
		} else {
//			alert("découpage des !");
			var T_Argument=Liste_Argument.split("!"); // création du tableau principal avec comme séparateur un !, tout autre caractère de séparation n'est pas pris en compte
		};
		
/*		// gestion du modèle Traffic et dérivés
		if (T_Argument[0] == "InterfaceID")
		{
			var T_TRAFFIC = T_Argument;
			//alert("T_TRAFFIC="+T_TRAFFIC);
		}
*/
		// gestion du modèle CPULOAD
		if (T_Argument[0] == "CPULOAD")
		{
			var T_CPU=T_Argument[1].split(","); // découpage de l'argument avec les virgules
			// reconstruction du tableau T_Argument
			var T_Argument = [];
			T_Argument[0]= T_CPU[1] + "," + T_CPU[4] + "," + T_CPU[7];
			T_Argument[1]= T_CPU[2] + "," + T_CPU[5] + "," + T_CPU[8];
		};
		// gestion du modèle CPULOAD
		if (T_Argument[0] == "MEMUSE")
		{
			var T_MEMOIRE=T_Argument; // découpage de l'argument avec les virgules
			// reconstruction du tableau T_Argument
			var T_Argument = [];
			T_Argument[0]= T_MEMOIRE[2];
			T_Argument[1]= T_MEMOIRE[3];
		};
		var NbLigne_T_Argument = T_Argument.length; // nb argument dans le tableau
		// boucle sur chacun des enregistrements puis redécoupe en un nouveau tableau
		for (var k=0;k<NbLigne_T_Argument;k++)
		{
			var l = k+1; // l correspond au numéro de fieldset service
			if (T_Argument[k] == "l"){ // controle Non récursif
				T_Argument[k]="Non";
			} else if (T_Argument[k] == "r") { // controle récursif
				T_Argument[k]="Oui";
			}
			alert(Service_Argument_id+"_"+l+"="+T_Argument[k]);
			$("#"+Service_Argument_id+"_"+l).val(T_Argument[k]); 
		};
		//return true;
	} else {
		alert("raté");
	};
};


function collecte_liste_plage(callback)
{
		var xhr_p = getXMLHttpRequest(); //création de l'instance XHR
		xhr_p.onreadystatechange = function() {
			if (xhr_p.readyState == 4 && (xhr_p.status == 200 || xhr_p.status == 0)) {
				$("#param_plage_horaire").empty();
				$("#param_plage_horaire").append(xhr_p.responseText);
//				$("#accordionPlage").empty();
//				$("#accordionPlage").append(xhr_p.responseText);
//				$( "#accordionPlage" ).accordion({ heightStyle: "content" });
//				collecte_liste_hote(rempli_hote); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
//				callback(xhr.responseText); // C'est bon \o/
//				$("#img_loading").remove();
				// réactivation du bouton "Valider Votre Sélection" pour permettre d'ajouter d'autres hôtes ou services
//				$("#Valider_Selection").removeAttr("Disabled");
//				alert("Chargement terminé! Vous pouvez passer au pamarétrage des hôtes et services sur l'onglet paramétrage'")
				//collecte_liste_service(); // fonction de remplissage de liste Param Hote => fonction_remplissage.js
			} else if(xhr_p.readyState == 4 && xhr_p.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_p.status + '\nTexte : ' + xhr_p.statusText);
			};
		};
//		alert("Nb_Hote:"+sNb_Hote);
		
		xhr_p.open("POST", "remplissage_param_plage.php", true);
		xhr_p.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//		xhr.send("Nb_Hote="+sNb_Hote+""); 
		xhr_p.send(); 

};


function rempli_plage(Liste_Plage)
{
//	Nb_Plage = resultat.substring(0,resultat.indexOf('$'));
//	Liste_Plage = resultat.substring(resultat.indexOf('$')+1);
//	alert("Nb_Plage="+Nb_Plage);
	//alert("liste_plage="+Liste_Plage);
	// découpe de la chaine retournée en deux variables
	if (Liste_Plage != ""){ 	// traitement si au moins 1 Hote est sélectionné
		var T_Plage=Liste_Plage.split("|"); // création du tableau principal
		var NbLigne_T_Plage = T_Plage.length; // nb hote dans le tableau
		var NbFieldset_Plage = $("fieldset.plage").length; // compte le nombre de fielset hote présent dans l'onglet paramétrage
		var Nb_Plage = NbLigne_T_Plage - NbFieldset_Plage;
		//alert("Ajout nouveau fieldset service => "+ Nb_Plage)
		for (var i = 0;i < Nb_Plage;i++)
		{
			ajoute_fieldset_plage();
		};
		// boucle sur chacun des enregistrements puis redécoupe en un nouveau tableau
		for (var i=0;i<NbLigne_T_Plage;i++)
		{
			var T_Plage_Lig = T_Plage[i].split("!"); // création tableau par ligne
			var j = i+1; // j correspond au numéro de fieldset hote
			//alert("plage="+T_Plage_Lig[0]);

	//			alert("implementation des valeurs");
				// implémentation des valeurs
				// ordre SELECT Nom_Service, Nom_Hote, Nom_Periode, Frequence, Consigne, Controle_Actif, MS.Modele_Service AS Modele_Service, MS.MS_Libelles AS MS_Libelles, Parametres
//				alert("Nom_Service"+j+"="+T_Service_Lig[0]);
			$("#Nom_Periode"+j).val(T_Plage_Lig[0]);
			$("#Lundi"+j).val(T_Plage_Lig[1]);
			$("#Mardi"+j).val(T_Plage_Lig[2]);
			$("#Mercredi"+j).val(T_Plage_Lig[3]);
			$("#Jeudi"+j).val(T_Plage_Lig[4]);
			$("#Vendredi"+j).val(T_Plage_Lig[5]);
			$("#Samedi"+j).val(T_Plage_Lig[6]);
			$("#Dimanche"+j).val(T_Plage_Lig[7]);

			//Desactivation des champs non modifiables
			$("#Nom_Periode"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Nom_Periode"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Nom_Periode"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Lundi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Lundi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Lundi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Mardi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Mardi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Mardi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Mercredi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Mercredi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Mercredi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Jeudi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Jeudi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Jeudi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Vendredi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Vendredi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Vendredi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Samedi"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Samedi"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Samedi"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

			$("#Dimanche"+j).attr("Disabled","Disabled"); // on verrouille la sélection
			$("#img_Dimanche"+j).attr("alt","correct"); // on force l'attribut et l'icone à OK
			$("#img_Dimanche"+j).attr("src","images/img_ok.png"); // on force l'attribut et l'icone à OK

		};
	};
};
