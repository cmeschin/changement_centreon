function Afficher_Masquer_DEC_groupee(ID_Date) 
{
	/**
	 * récupère la liste des demandes traitées pour le mois sélectionné
	 * puis appelle en boucle la fonction classique de remplissage du tableau 
	 */
	
	if (!$( "#DEC_Detail_groupee" + ID_Date + "").attr("Selected")){
		collecte_DEC_liste_groupee(ID_Date); // collecte des infos générales complémentaires
		$( "#DEC_Detail_groupee" + ID_Date + "").show( "fold", 1000 );
		$( "#DEC_Detail_groupee" + ID_Date + "").attr("Selected","Selected");
	} else {
		$( "#DEC_Detail_groupee" + ID_Date + "").hide( "fold", 1000 );
		$( "#DEC_Detail_groupee" + ID_Date + "").removeAttr("Selected");
	};
};

function collecte_DEC_liste_groupee(ID_Date)
{
	var xhr_i = getXMLHttpRequest(); //création de l'instance XHR
	xhr_i.onreadystatechange = function() {
		if (xhr_i.readyState == 4 && (xhr_i.status == 200 || xhr_i.status == 0)) {
			$("#DEC_liste_groupee" + ID_Date + "").empty();
			$("#DEC_liste_groupee" + ID_Date + "").append(xhr_i.responseText);
		} else if(xhr_i.readyState == 4 && xhr_i.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_i.status + '\nTexte : ' + xhr_i.responseText);
		};
	};

	xhr_i.open("POST", "liste_demande_traitees_par_mois.php", true);
	xhr_i.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_i.send("ID_Date="+ID_Date+""); 
};


function Afficher_Masquer_DEC(ID_Demande) 
{
	if (!$( "#DEC_Detail" + ID_Demande + "").attr("Selected")){
		collecte_DEC_infos(ID_Demande); // collecte des infos générales complémentaires
		collecte_DEC_hote(ID_Demande);
		collecte_DEC_service(ID_Demande);
		collecte_DEC_plage(ID_Demande);
		$( "#DEC_Detail" + ID_Demande + "").show( "fold", 1000 );
		$( "#DEC_Detail" + ID_Demande + "").attr("Selected","Selected");
	} else {
		$( "#DEC_Detail" + ID_Demande + "").hide( "fold", 1000 );
		$( "#DEC_Detail" + ID_Demande + "").removeAttr("Selected");
	};
};

function collecte_DEC_infos(ID_Demande)
{
	var xhr_i = getXMLHttpRequest(); //création de l'instance XHR
	xhr_i.onreadystatechange = function() {
		if (xhr_i.readyState == 4 && (xhr_i.status == 200 || xhr_i.status == 0)) {
			$("#DEC_infos" + ID_Demande + "").empty();
			$("#DEC_infos" + ID_Demande + "").append(xhr_i.responseText);
		} else if(xhr_i.readyState == 4 && xhr_i.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_i.status + '\nTexte : ' + xhr_i.responseText);
		};
	};

	xhr_i.open("POST", "remplissage_DEC_infos.php", true);
	xhr_i.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_i.send("ID_Demande="+ID_Demande+""); 
};

function collecte_DEC_hote(ID_Demande)
{
	var xhr_h = getXMLHttpRequest(); //création de l'instance XHR
	xhr_h.onreadystatechange = function() {
		if (xhr_h.readyState == 4 && (xhr_h.status == 200 || xhr_h.status == 0)) {
			$("#DEC_hote" + ID_Demande + "").empty();
			$("#DEC_hote" + ID_Demande + "").append(xhr_h.responseText);
		} else if(xhr_h.readyState == 4 && xhr_h.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_h.status + '\nTexte : ' + xhr_h.responseText);
		};
	};

	xhr_h.open("POST", "remplissage_DEC_hote.php", true);
	xhr_h.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_h.send("ID_Demande="+ID_Demande+""); 
};

function collecte_DEC_service(ID_Demande)
{
	var xhr_s = getXMLHttpRequest(); //création de l'instance XHR
	xhr_s.onreadystatechange = function() {
		if (xhr_s.readyState == 4 && (xhr_s.status == 200 || xhr_s.status == 0)) {
			$("#DEC_service" + ID_Demande + "").empty();
			$("#DEC_service" + ID_Demande + "").append(xhr_s.responseText);
			//$(".verif").remove();
			//$("input:text[class*='Service_Argument']").attr("Readonly","Readonly");
			//$("fieldset[id*='Inactif_Arg_Service_Modele']").remove();
		} else if(xhr_s.readyState == 4 && xhr_s.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_s.status + '\nTexte : ' + xhr_s.responseText);
		};
	};
	
	xhr_s.open("POST", "remplissage_DEC_service.php", true);
	xhr_s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_s.send("ID_Demande="+ID_Demande+""); 
};

function collecte_DEC_plage(ID_Demande)
{
	var xhr_p = getXMLHttpRequest(); //création de l'instance XHR
	xhr_p.onreadystatechange = function() {
		if (xhr_p.readyState == 4 && (xhr_p.status == 200 || xhr_p.status == 0)) {
			$("#DEC_plage" + ID_Demande + "").empty();
			$("#DEC_plage" + ID_Demande + "").append(xhr_p.responseText);
		} else if(xhr_p.readyState == 4 && xhr_p.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_p.status + '\nTexte : ' + xhr_p.responseText);
		};
	};

	xhr_p.open("POST", "remplissage_DEC_plage.php", true);
	xhr_p.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_p.send("ID_Demande="+ID_Demande+""); 
};
