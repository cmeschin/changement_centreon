//function collecte_param_global(callback) // appelé par fonction_enregistrer.js
//{
//	var xhr_g = getXMLHttpRequest(); //création de l'instance XHR globale
//	xhr_g.onreadystatechange = function() {
//		if (xhr_g.readyState == 4 && (xhr_g.status == 200 || xhr_g.status == 0)) {
//			$("#accordionParam").empty();
//			$("#accordionParam").append(xhr_g.responseText);
//		} else if(xhr_g.readyState == 4 && xhr_g.status != 200) { // En cas d'erreur !
//			alert('Une erreur est survenue !\n\nCode :' + xhr_g.status + '\nTexte : ' + xhr_g.responseText);
//		};
//	};
//	xhr_g.open("POST", "remplissage_param_global.php", true);
//	xhr_g.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//	xhr_g.send(); 
//};

// dépréciée le 18/03/15 par la fonction globale ci-dessus => réactivé le 25/03 pose pb avec les accordion jquery
function collecte_liste_hote(callback) // appelé par fonction_enregistrer.js
{
	var xhr_h = getXMLHttpRequest(); //création de l'instance XHR
	xhr_h.onreadystatechange = function() {
		if (xhr_h.readyState == 4 && (xhr_h.status == 200 || xhr_h.status == 0)) {
			$("#param_hote").empty();
			$("#param_hote").append(xhr_h.responseText);
		} else if(xhr_h.readyState == 4 && xhr_h.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_h.status + '\nTexte : ' + xhr_h.responseText);
		};
	};
	xhr_h.open("POST", "remplissage_param_hote.php", true);
	xhr_h.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_h.send(); 
};

function collecte_liste_service(callback) // appelé par fonction_enregistrer.js
{
	var xhr_s = getXMLHttpRequest(); //création de l'instance XHR
	xhr_s.onreadystatechange = function() {
		if (xhr_s.readyState == 4 && (xhr_s.status == 200 || xhr_s.status == 0)) {
			$("#param_service").empty();
			$("#param_service").append(xhr_s.responseText);
		} else if(xhr_s.readyState == 4 && xhr_s.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_s.status + '\nTexte : ' + xhr_s.responseText);
		};
	};
	xhr_s.open("POST", "remplissage_param_service.php", true);
	xhr_s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_s.send(); 
};

function collecte_liste_plage(callback) // appelé par fonction_enregistrer.js
{
	var xhr_p = getXMLHttpRequest(); //création de l'instance XHR
	xhr_p.onreadystatechange = function() {
		if (xhr_p.readyState == 4 && (xhr_p.status == 200 || xhr_p.status == 0)) {
			$("#param_plage_horaire").empty();
			$("#param_plage_horaire").append(xhr_p.responseText);
		} else if(xhr_p.readyState == 4 && xhr_p.status != 200) { // En cas d'erreur !
			alert('Une erreur est survenue !\n\nCode :' + xhr_p.status + '\nTexte : ' + xhr_p.responseText);
		};
	};
	xhr_p.open("POST", "remplissage_param_plage.php", true);
	xhr_p.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_p.send(); 
};
