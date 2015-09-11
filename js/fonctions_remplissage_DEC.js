function Afficher_Masquer_DEC(ID_Dem) {
			if (!$( "#DEC_Detail" + ID_Dem + "").attr("Selected")){
				collecte_DEC_infos(ID_Dem); // collecte des infos générales complémentaires
				collecte_DEC_hote(ID_Dem);
				collecte_DEC_service(ID_Dem);
				collecte_DEC_plage(ID_Dem);
				$( "#DEC_Detail" + ID_Dem + "").show( "fold", 1000 );
				$( "#DEC_Detail" + ID_Dem + "").attr("Selected","Selected");
			} else {
				$( "#DEC_Detail" + ID_Dem + "").hide( "fold", 1000 );
				$( "#DEC_Detail" + ID_Dem + "").removeAttr("Selected");
			}
		}

function collecte_DEC_infos(ID_Dem) // appelé par fonction_enregistrer.js, appelle rempli_hote
{
		var xhr_i = getXMLHttpRequest(); //création de l'instance XHR
		xhr_i.onreadystatechange = function() {
			if (xhr_i.readyState == 4 && (xhr_i.status == 200 || xhr_i.status == 0)) {
				$("#DEC_infos" + ID_Dem + "").empty();
				$("#DEC_infos" + ID_Dem + "").append(xhr_i.responseText);
			} else if(xhr_i.readyState == 4 && xhr_i.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_i.status + '\nTexte : ' + xhr_i.responseText);
			};
		};

		xhr_i.open("POST", "remplissage_DEC_infos.php", true);
		xhr_i.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_i.send("ID_Dem="+ID_Dem+""); 
};

function collecte_DEC_hote(ID_Dem) // appelé par fonction_enregistrer.js, appelle rempli_hote
{
		var xhr_h = getXMLHttpRequest(); //création de l'instance XHR
		xhr_h.onreadystatechange = function() {
			if (xhr_h.readyState == 4 && (xhr_h.status == 200 || xhr_h.status == 0)) {
				$("#DEC_hote" + ID_Dem + "").empty();
				$("#DEC_hote" + ID_Dem + "").append(xhr_h.responseText);
			} else if(xhr_h.readyState == 4 && xhr_h.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_h.status + '\nTexte : ' + xhr_h.responseText);
			};
		};

		xhr_h.open("POST", "remplissage_DEC_hote.php", true);
		xhr_h.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_h.send("ID_Dem="+ID_Dem+""); 
};

function collecte_DEC_service(ID_Dem) // appelé par fonction_enregistrer.js, appelle rempli_service
{
		var xhr_s = getXMLHttpRequest(); //création de l'instance XHR
		xhr_s.onreadystatechange = function() {
			if (xhr_s.readyState == 4 && (xhr_s.status == 200 || xhr_s.status == 0)) {
				$("#DEC_service" + ID_Dem + "").empty();
				$("#DEC_service" + ID_Dem + "").append(xhr_s.responseText);
				$(".verif").remove();
				$("input:text[class*='Service_Argument']").attr("Readonly","Readonly");
				$("fieldset[id*='Inactif_Arg_Service_Modele']").remove();
			} else if(xhr_s.readyState == 4 && xhr_s.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_s.status + '\nTexte : ' + xhr_s.responseText);
			};
		};
		
		xhr_s.open("POST", "remplissage_DEC_service.php", true);
		xhr_s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_s.send("ID_Dem="+ID_Dem+""); 

};

function collecte_DEC_plage(ID_Dem)
{
		var xhr_p = getXMLHttpRequest(); //création de l'instance XHR
		xhr_p.onreadystatechange = function() {
			if (xhr_p.readyState == 4 && (xhr_p.status == 200 || xhr_p.status == 0)) {
				$("#DEC_plage" + ID_Dem + "").empty();
				$("#DEC_plage" + ID_Dem + "").append(xhr_p.responseText);
			} else if(xhr_p.readyState == 4 && xhr_p.status != 200) { // En cas d'erreur !
				alert('Une erreur est survenue !\n\nCode :' + xhr_p.status + '\nTexte : ' + xhr_p.responseText);
			};
		};

		xhr_p.open("POST", "remplissage_DEC_plage.php", true);
		xhr_p.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr_p.send("ID_Dem="+ID_Dem+""); 

};
