function afficher_autre(champ)
{
	var ma_selection= document.getElementById(champ.id).options[document.getElementById(champ.id).selectedIndex];
 	if(ma_selection.value =="Autre")
 	{
		$("#" + champ.id + "_new input[type=text]").val(""); // on force le champ à vide
		$("#img_" + champ.id + "_new").attr("src","images/img_edit.png");
		$("#img_" + champ.id + "_new").attr("alt","incorrect");
		document.getElementById(champ.id + '_new').style.visibility = 'visible';
	} else if(ma_selection.value =="")
	{
		if (document.getElementById(champ.id + '_new'))
		{
			document.getElementById(champ.id + '_new').style.visibility = 'hidden';
			$("#" + champ.id + "_new input[type=text]").val("Vide"); // en cas de changement on vide le champ "nouveau"
			$("#img_" + champ.id + "_new").attr("src","images/img_ok.png");
			$("#img_" + champ.id + "_new").attr("alt","correct");
		};
	} else
	{
		if (document.getElementById(champ.id + '_new'))
		{
			document.getElementById(champ.id + '_new').style.visibility = 'hidden';
			$("#" + champ.id + "_new input[type=text]").val("Vide"); // en cas de changement on vide le champ "nouveau"
			$("#img_" + champ.id + "_new").attr("src","images/img_ok.png");
			$("#img_" + champ.id + "_new").attr("alt","correct");
		};
	};
};

function chargerlistes()
{
	var ma_selection= document.getElementById('clientsup').options[document.getElementById('clientsup').selectedIndex];
/**
 * Désactivation option "Nouveau" version 8.4
 */
// 	if(ma_selection.value =="Nouveau")
// 	{
//		$("#clientsup").removeAttr("class");
//		$("#img_client_new").attr("src","images/img_edit.png");
//		$("#img_client_new").attr("alt","incorrect");
//		document.getElementById('sclient_new').style.visibility = 'visible'; // affiche le span nouveau client
//		document.getElementById('client_new').focus(); // positionne le focus sur l'input
//		
//		$("#liste_hote").empty(); // purge la liste à chaque nouvelle sélection de prestation
////		alert("test");
//
//		$("#liste_hote").append('<table id="T_Liste_Hote"><tr><th>Sélection</th><th>Hôte</th><th>Description</th><th>Adresse IP</th><th>Controle</th><th hidden>host_id</th></tr></table>');// Hôte
//		$("#liste_service").empty(); // purge la liste à chaque nouvelle sélection de prestation
//		$("#liste_service").append('<table id="T_Liste_Service"><tr><th>Selection</th><th>Hôte</th><th>Service</th><th>Fréquence</th><th>Plage Horaire</th><th>Controle</th></tr></table>');
//		$("#liste_plage").empty(); // purge la liste à chaque nouvelle sélection de prestation
//		$("#liste_plage").append('<table id="T_Liste_Plage"><tr><th>Sélection</th><th>Plage Horaire</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th></tr></table>');
//	} else
	if(ma_selection.value =="")
	{
		//document.getElementById('sclient_new').style.visibility = 'hidden'; // masque le span nouveau client
		//$("#client_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau client"
		$("#clientsup").attr("class","info_generale");
		//$("#img_client_new").attr("src","images/img_ok.png");
		//$("#img_client_new").attr("alt","correct");
//		alert("test");
		$("#liste_hote").empty(); // purge la liste à chaque nouvelle sélection de prestation
        $("#liste_hote").append('<table id="T_Liste_Hote"><tr><th>Sélection</th><th>Hôte</th><th>Description</th><th>Adresse IP</th><th>Controle</th><th hidden>host_id</th></tr></table>');// Hôte
		$("#liste_service").empty(); // purge la liste à chaque nouvelle sélection de prestation
        $("#liste_service").append('<table id="T_Liste_Service"><tr><th>Selection</th><th>Hôte</th><th>Service</th><th>Fréquence</th><th>Plage Horaire</th><th>Controle</th></tr></table>');
        $("#liste_plage").empty(); // purge la liste à chaque nouvelle sélection de prestation
        $("#liste_plage").append('<table id="T_Liste_Plage"><tr><th>Sélection</th><th>Plage Horaire</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th></tr></table>');
	} else
	{
		//document.getElementById('sclient_new').style.visibility = 'hidden'; // masque le span nouveau client
		$("#clientsup").attr("class","info_generale");
		//$("#img_client_new").attr("src","images/img_ok.png");
		//$("#img_client_new").attr("alt","correct");
		//$("#client_new input[type=text]").val(""); // en cas de changement on vide le champ "nouveau client"
		var sClient = encodeURIComponent(ma_selection.value);
//		requete_hote(readData_hote);
//		requete_service(readData_service);
//		requete_plage(readData_plage,sClient);
		requete_hote(readData_hote,sClient);
	};
};

function requete_hote(callback,sClient)
{
	var xhr_h = getXMLHttpRequest(); //création de l'instance XHR
	xhr_h.onreadystatechange = function()
	{
		if (xhr_h.readyState == 4 && (xhr_h.status == 200 || xhr_h.status == 0))
		{
			callback(xhr_h.responseText); // C'est bon \o/
			requete_plage(readData_plage,sClient);
		};
	};
//	var sClient = encodeURIComponent(ma_selection.value);
	xhr_h.open("POST", "requete_liste_hote.php", true);
	xhr_h.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_h.send("monclient="+sClient+""); 
};

function requete_service(callback,sClient)
{
	var xhr_s = getXMLHttpRequest(); //création de l'instance XHR
	xhr_s.onreadystatechange = function()
	{
		if (xhr_s.readyState == 4 && (xhr_s.status == 200 || xhr_s.status == 0))
		{
			callback(xhr_s.responseText); // C'est bon \o/
		};
	};
//	var sClient = encodeURIComponent(ma_selection.value);
	xhr_s.open("POST", "requete_liste_service.php", true);
	xhr_s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_s.send("monclient="+sClient+""); 
};

function requete_plage(callback,sClient)
{
	var xhr_p = getXMLHttpRequest(); //création de l'instance XHR
	xhr_p.onreadystatechange = function()
	{
		if (xhr_p.readyState == 4 && (xhr_p.status == 200 || xhr_p.status == 0))
		{
			callback(xhr_p.responseText); // C'est bon \o/
			requete_service(readData_service,sClient);
		};
	};
//	var sClient = encodeURIComponent(ma_selection.value);
	xhr_p.open("POST", "requete_liste_plage.php", true);
	xhr_p.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr_p.send("monclient="+sClient+""); 
};

function readData_hote(liste_hote)
{
	$("#liste_hote").empty(); // purge la liste à chaque nouvelle sélection de prestation
	$("#liste_hote").append(liste_hote); // rempli la liste avec la sélection courante
};
function readData_service(liste_service)
{
	$("#liste_service").empty(); // purge la liste à chaque nouvelle sélection de prestation
	$("#liste_service").append(liste_service); // rempli la liste avec la sélection courante
};
function readData_plage(liste_plage)
{
	$("#liste_plage").empty(); // purge la liste à chaque nouvelle sélection de prestation
	$("#liste_plage").append(liste_plage); // rempli la liste avec la sélection courante
};

function chargerlistes_Ajout()
{
	var tableau_liste = document.getElementById("T_Liste_Hote");
	var tableau_Recherche_Lig = document.getElementById("T_Liste_Recherche_Hote").rows; //stockage du tableau recherche
	var NbLigne = tableau_Recherche_Lig.length; // récupération du nombre d'enregistrement
	var nbcheck = $('input:checked[name=selection]').size();
	var MessageConfirmation = "Vous allez ajouter " + nbcheck + " hôte(s) ainsi que tous les services et les plages horaires associés. Etes-vous sûr?";
	$('input:checked[name=selection_hote]').each(function() // on boucle sur chaque objet sélectionné dans liste hôte et on les désélectionne afin d'éviter d'ajouter des hôtes à tord
	{
		$(this).prop('checked', false);
	});
	$('input:checked[name=selection_service]').each(function() // idem pour les services
	{
		$(this).prop('checked', false);
	});
	$('input:checked[name=selection_plage]').each(function() // idem pour les plages
	{
		$(this).prop('checked', false);
	});

	if (nbcheck == 0)
	{ var Message = "Il n'y a aucun hôte sélectionné!";
		alert(Message);
	} else 
	{
		if (confirm(MessageConfirmation)) 
		{
			for (var i=1; i<NbLigne;i++) // l'index 0 étant le titre du tableau, on commence à 1
			{
				var tableau_Recherche_Col = tableau_Recherche_Lig[i].cells; // on affecte dans un nouveau tableau colonne les cellules de chaque ligne
				$('input:checked[id='+i+']').each(function() // on boucle sur chaque objet sélectionné
				{
					var existant = false; // on réinitialise la variable à chaque nouvelle entrée du tableau
//					var tableau_liste_Lig = document.getElementById("T_Liste_Hote").rows; // on charge dans un tableau les lignes du tableau liste hôte
	                var tableau_liste_Lig = tableau_liste.rows; // on charge dans un tableau les lignes du tableau liste hôte
					var NbLigne_liste = tableau_liste_Lig.length;

					// on boucle sur les élements liste hôte pour vérifier les existants
					for (var j=1; j<NbLigne_liste;j++) // l'index 0 étant le titre du tableau, on commence à 1
					{
						var tableau_liste_Col = tableau_liste_Lig[j].cells;
						//if (tableau_Recherche_Col[1].innerHTML == tableau_liste_Col[1].innerHTML) // on vérifie si l'hôte à ajouter n'existe pas déjà
						if (tableau_Recherche_Col[3].innerHTML == tableau_liste_Col[3].innerHTML) // on vérifie si l'hôte à ajouter n'existe pas déjà
						{
							//var existant=true;
							existant = true;
						};
					};

					if (existant == true) // l'hôte sélectionné existe déjà, on ne l'insère pas.
					{
						//var message = "L'hôte [" + tableau_Recherche_Col[1].innerHTML + "] existe déjà dans la liste, il ne sera pas ajouté.";
						var message = "L'hôte [" + tableau_Recherche_Col[3].innerHTML + "] existe déjà dans la liste, il ne sera pas ajouté.";
						alert(message);
						$(this).attr('checked',false);
					} else // l'hôte sélectionné n'existe pas, on l'insère.
					{
						//ID_Hote = tableau_Recherche_Col[5].innerHTML;
						ID_Hote = tableau_Recherche_Col[7].innerHTML;
						var ligne = tableau_liste.insertRow(-1);//on a ajouté une ligne à la fin
						var colonne1 = ligne.insertCell(0);//on ajoute la première cellule
						colonne1.innerHTML += '<input type="checkbox" name="selection_hote" id="'+NbLigne_liste+'"/>';// Hôte
						var colonne2 = ligne.insertCell(1);//on ajoute la seconde cellule
						colonne2.innerHTML += tableau_Recherche_Col[1].innerHTML;// Localisation
						//colonne2.innerHTML += substring(5,tableau_Recherche_Col[1].innerHTML.indexOf("-",5));// Hôte sans localisation et type (extraction après le deuxième tiret)
						var colonne3 = ligne.insertCell(2);
						colonne3.innerHTML += tableau_Recherche_Col[2].innerHTML;//Type
						var colonne4 = ligne.insertCell(3);
						colonne4.innerHTML += tableau_Recherche_Col[3].innerHTML;//Hôte
						var colonne5 = ligne.insertCell(4);
						colonne5.innerHTML += tableau_Recherche_Col[4].innerHTML;//Description
						var colonne6 = ligne.insertCell(5);
						colonne6.innerHTML += tableau_Recherche_Col[5].innerHTML;//IP
						var colonne7 = ligne.insertCell(6);
						colonne7.innerHTML += tableau_Recherche_Col[6].innerHTML;//Controle
						var colonne8 = ligne.insertCell(7);
						colonne8.innerHTML += 'h'+ID_Hote;//Id_hote
						colonne8.style.display = 'none'; // masque la nouvelle colonne
						if (colonne7.innerHTML == "inactif")
						{
							$(colonne7).parent().attr("class", "inactif" );
						};
						// ajouter la liste des services associés à l'hôte inséré.
						chargerlistes_Complement_Services(ID_Hote);
						// ajouter la liste des plages associées aux différents service insérés.
						chargerlistes_Complement_Plages(ID_Hote);

						$(this).attr('checked',false);
					};
				});
			};
		//var MessagePurge = "La sélection a été ajoutée, la liste de recherche va âtre purgée.";
		//alert(MessagePurge);
		$("#liste_recherche_hote").empty(); // purge la liste recherchée une fois l'ajout effectué
		};
	};
}; 

function chargerlistes_Complement_Services()
{
 	function requete_service(callback) 
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
				callback(xhr.responseText); // C'est bon \o/
			};
		};
		var tableau_liste_Service_Lig = document.getElementById("T_Liste_Service").rows; // on charge dans un tableau les lignes du tableau liste hôte
		var NbService = tableau_liste_Service_Lig.length; // permet de continuer le tableau à la suite
//		alert(NbService);
		var sID_Hote = encodeURIComponent(ID_Hote);
		var sNbService = encodeURIComponent(NbService);
		xhr.open("POST", "requete_liste_recherche_service.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("sID_Hote="+sID_Hote+"&sNbService="+sNbService); 
	};
	function readData_service(liste_service) 
	{
		//alert(liste_service);
		var T_service=liste_service.split("|");
		//alert(T_service);
		var NbLigne_T_service = T_service.length;
		var tableau_service = document.getElementById("T_Liste_Service");
		//alert(NbLigne_T_service);
		for (var i=0;i<NbLigne_T_service;i++)
		{
			var T_service_Lig = T_service[i].split("!");
			//alert(i+':'+T_service_Lig[2]);

			var ligne = tableau_service.insertRow(-1);//on ajoute une ligne à la fin du tableau
			var colonne1 = ligne.insertCell(0);//on ajoute la première cellule
			colonne1.innerHTML += T_service_Lig[0];// checkbox
			var colonne2 = ligne.insertCell(1);//on ajoute la seconde cellule
			// si hote à inscrire on découpe la chaine pour extraire l'IP et la localisation pour le popup
			if (T_service_Lig[1] != "")
			{
				var position_tiret1 = T_service_Lig[1].indexOf('-'); // position du premier tiret dans la chaine
				var position_tiret2 = T_service_Lig[1].indexOf('-',position_tiret1+1); // position du premier tiret dans la chaine
				var position_espace = T_service_Lig[1].indexOf(' '); // position de l'espace dans la chaine
				
				var localisation = T_service_Lig[1].substring(0,position_tiret1); // conserve la chaine avant le premier tiret
				var hote_type = T_service_Lig[1].substring(position_tiret1+1,position_tiret2); // conserve la chaine entre les deux tirets
				var nom_hote = T_service_Lig[1].substring(position_tiret2+1,position_espace); // conserve la chaine après le second tiret et avant l'espace
				var hote_ip = T_service_Lig[1].substring(position_espace+1); // conserve la chaine après l'espace
				
				colonne2.innerHTML += nom_hote;// Hôte (renseigné 1/10)
				$(colonne2).attr("title","" + hote_ip + " - " + localisation + "");
			} else
			{
				colonne2.innerHTML += T_service_Lig[1];
			};
			var colonne3 = ligne.insertCell(2);
			colonne3.innerHTML += T_service_Lig[2];//Service
			var colonne4 = ligne.insertCell(3);
			colonne4.innerHTML += T_service_Lig[3];//Frequence
			var colonne5 = ligne.insertCell(4);
			colonne5.innerHTML += T_service_Lig[4];//Plage_Horaire
			var colonne6 = ligne.insertCell(5);
			colonne6.innerHTML += T_service_Lig[5];//Controle
			var colonne7 = ligne.insertCell(6);
			colonne7.innerHTML += T_service_Lig[6];//service_id
			var colonne8 = ligne.insertCell(7);
			colonne8.innerHTML += T_service_Lig[7];//host_id
			colonne7.style.display = 'none'; // masque la nouvelle colonne
			colonne8.style.display = 'none'; // masque la nouvelle colonne
			
			if (colonne6.innerHTML == "inactif")
			{
				$(colonne6).parent().attr("class","inactif");
			};
		};
	};
	requete_service(readData_service);
};

function chargerlistes_Complement_Plages()
{
 	function requete_plage(callback) 
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
				callback(xhr.responseText); // C'est bon \o/
			};
		};

		var t_lst_hote = document.getElementById("T_Liste_Hote").rows; // charge la liste des hôtes insérés pour en extraire les id
		var Nbhote= t_lst_hote.length; // nombre d'hôte du tableau (inclu l'entête)
		var lst_id_hote='';
		for (var i=1; i<Nbhote;i++) // l'index 0 étant le titre du tableau, on commence à 1
			{
				var t_lst_hote_Col = t_lst_hote[i].cells; // extraction de chaque cellule du tableau dhôte
				var id_hote = t_lst_hote_Col[7].innerHTML;
				lst_id_hote += id_hote.substring(1) + ','; // construit la liste des id_hote.
			};
			lst_id_hote = lst_id_hote.substring(0,lst_id_hote.length-1);
		var slst_id_hote = encodeURIComponent(lst_id_hote);
		xhr.open("POST", "requete_liste_recherche_plage.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("slst_id_hote="+slst_id_hote); 
	};

	function readData_plage(liste_plage) 
	{
		$("#liste_plage").empty(); // purge la liste à chaque nouvelle insertion d'hôte
		$("#liste_plage").append(liste_plage); // rempli la liste avec la sélection courante
	};

	requete_plage(readData_plage);
};

function ajoute_fieldset_hote(liste_hote)
{
	timer_enregistrement();
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
//				alert("preparation insertion automatique");
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
		{
			if (numtrou < h) // si le numhote à ajouter est inférieur au num hote max alors on insère dans le trou
			{ 
				// premierhote correspond au premierhote trouvé après le trou
				// numtrou correspond au dernier numhote sans trou
				$("#"+premierhote).before(xhr.responseText);
				var numhote = numtrou;
				//alert("premierhote="+premierhote);
				//alert("avant numtrou="+numhote);
				//alert("xhr="+xhr.responseText);
				//return xhr.responseText;
				
			} else if (numtrou>0)
			{
				$("#"+premierhote).after(xhr.responseText);
				//alert("premierhote="+premierhote);
				var numhote = numtrou;
				//alert("apres numtrou="+numhote);
				//alert("xhr="+xhr.responseText);
				//return xhr.responseText;
			} else
			{
				$("#param_hote").append(xhr.responseText);
				var numhote = numtrou;
				//alert("fin numtrou="+numhote);
				//alert("xhr="+xhr.responseText);
				//return xhr.responseText;
			};
			//return xhr.responseText;
			//alert("affiche");
			//return numtrou;
		};
	};
// compter le nombre total de fieldset
	var trou=false;
	var NbFieldset = $("fieldset.hote").length;
//	alert("NbFieldset="+NbFieldset);
// boucler sur chaque fieldset pour identifier les trous
    queryAll = document.querySelectorAll('.hote');
	for (var i = 0; i< NbFieldset; i++){
		var h = i+1;
		if (( "Hote"+ h != queryAll[i].id) && (!trou)) {
	// Ajouter le fieldset dans le premier trou trouvé
			var numtrou = i;
			var hotetrou = "Hote"+h;
//			alert("hotetrou="+hotetrou);
			var premierhote = queryAll[i].id; // on stocke le premier hote trouvé après le trou
			trou=true; 
			nouveau_hote(numtrou);
			break;
		};
	};
	if (!trou){ // pas de trou dans la liste
//		alert("Pas de trou");
		numtrou = NbFieldset;
		premierhote = "Hote"+numtrou;
		nouveau_hote(numtrou);
	};
	function nouveau_hote(numtrou){
		numtrou = encodeURIComponent(numtrou);
		xhr.open("POST", "modele_param_hote.php", false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("NbFieldset="+numtrou+"");
	};
};

function ajoute_fieldset_service() {
	timer_enregistrement();
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
				//alert("preparation insertion automatique");
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			if (numtrou < h) // si le numservice à ajouter est inférieur au num service max alors on insère dans le trou
			{ 
				$("#"+premierservice).before(xhr.responseText);
			} else if (numtrou>0)
			{
				$("#"+premierservice).after(xhr.responseText);
			} else
			{
				$("#param_service").append(xhr.responseText);
			};
			return xhr.responseText;
		};
	};
// compter le nombre total de fieldset
	var trou=false;
	var NbFieldset_Service = $("fieldset.service").length;
//	alert("NbFieldset_Service="+NbFieldset_Service);
// boucler sur chaque fieldset pour identifier les trous
    queryAll = document.querySelectorAll('.service');
	for (var i = 0; i< NbFieldset_Service; i++){
		var h = i+1;
		if (( "Service"+ h != queryAll[i].id) && (!trou)) 
		{
			// Ajouter le fieldset dans le premier trou trouvé
			var numtrou = i;
			var servicetrou = "Service"+h;
//			alert("servicetrou="+servicetrou);
			var premierservice = queryAll[i].id; // on stocke le premier service trouvé après le trou
			trou=true; 
			nouveau_service(numtrou);
			break;
		};
	};
	if (!trou)
	{ // pas de trou dans la liste
//		alert("Pas de trou");
		numtrou = NbFieldset_Service;
		premierservice = "Service"+numtrou;
		nouveau_service(numtrou);
	};
	function nouveau_service(numtrou)
	{
		numtrou = encodeURIComponent(numtrou);
		xhr.open("POST", "modele_param_service.php", false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("NbFieldset_Service="+numtrou+"");
	};
};

function ajoute_fieldset_plage() 
{
	timer_enregistrement();
	// suppression de l'éventuelle message pas de plage
	$("#Aucune_Plage").remove();
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			if (numtrou < h) // si le numhote à ajouter est inférieur au num hote max alors on insère dans le trou
			{ 
				$("#"+premierePlage).before(xhr.responseText);
			} else if (numtrou>0)
			{
				$("#"+premierePlage).after(xhr.responseText);
			} else
			{
				$("#param_plage_horaire").append(xhr.responseText);
			};
			return xhr.responseText;
		};
	};
// compter le nombre total de fieldset
	var trou=false;
	var NbFieldset_plage = $("fieldset.plage").length; // on compte tous les lien de class "plage"
	//alert(NbFieldset_plage);
// boucler sur chaque fieldset pour identifier les trous
	queryAll = document.querySelectorAll('.plage');
	for (var i = 0; i< NbFieldset_plage; i++)
	{
		var h = i+1;
		if (( "Plage"+ h != queryAll[i].id) && (!trou)) 
		{
	// Ajouter le fieldset dans le premier trou trouvé
			var numtrou = i;
			var plagetrou = "Plage"+h;
			var premierePlage = queryAll[i].id; // on stocke la premiere plage trouvé après le trou
			trou=true; 
//			var MessageConfirmation = "Vous allez ajouter la " + parent_plage + " . Etes-vous sûr?";
//			if (confirm(MessageConfirmation)) {
				nouveau_plage(numtrou);
				break;
//			};
		};
	};
	if (!trou)
	{ // pas de trou dans la liste
		numtrou = NbFieldset_plage;
		premierePlage = "Plage"+numtrou;
		nouveau_plage(numtrou);
	};
	function nouveau_plage(numtrou)
	{
		numtrou = encodeURIComponent(numtrou);
		xhr.open("POST", "modele_param_plage.php", false);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("NbFieldset_plage="+numtrou+"");
	};
};

function supprime_fieldset_hote(champ) 
{
	timer_enregistrement();
	var parent=$(champ).parent().parent().attr("id");

	var MessageConfirmation = "Vous allez supprimer l'" + parent + " . Etes-vous sûr?";
	if (confirm(MessageConfirmation)) 
	{
		var Nom_Hote = $('input[id=Nom_'+parent+']').val();
		var IP_Hote = $('input[id=IP_'+parent+']').val();
		var Hote_Nom_IP = Nom_Hote + ' - ' + IP_Hote;
		var nbdisabled_hote = $('input:disabled[name=selection_hote]').size(); // récupère le nombre d'hôte désactivés

		if (nbdisabled_hote > 0) // ne rentre dans la boucle que si des hôtes sont désactivés
		{
			/**
			 *  gestion des hôtes importés pour réactivation dans la liste
			 */
			var tableau_hote_lig = document.getElementById("T_Liste_Hote").rows; //charge les lignes du tableau
			var NbLigne_Hote = tableau_hote_lig.length; // récupération du nombre d'enregistrement
			for (var i=1;i<NbLigne_Hote;i++)
			{
				$('input:disabled[id=' + i +']').each(function() // on boucle sur chaque objet sélectionné
				{
					var tableau_hote_col = tableau_hote_lig[i].cells; //charge les cellules de chaque ligne désactivée dans un tableau
					if (tableau_hote_col[3].innerHTML+tableau_hote_col[5].innerHTML == Nom_Hote+IP_Hote)
					{
						$(this).removeAttr("Disabled"); // réactive l'hôte inséré pour permettre un nouvel ajout.
					};
				});
			};
		};
//code désactivé car on doit garder l'hôte utilisable pour les services même si on ne le change pas
// cas à gérer si c'est un nouvel ajout (bouton Preenregistrer visible)
/*		// on supprime des listes hote_service l'enregistrement
		$("select[id*='Hote_Service']").each(function()
		{
			s_id_hote=$(this).attr("id");
			//alert("s_id_hote="+s_id_hote);
			//alert("Nom_Hote=" + Nom_Hote);
			//$("#"+s_id_hote+" option[value='"+Nom_Hote+"']").remove(); // on supprime de chaque liste déroulante l'hôte
			$("#"+s_id_hote+" option[value='"+ID_Hote+"']").remove(); // on supprime de chaque liste déroulante l'hôte
		});

		// on supprime de la table hote_temp pour ne plus l'ajouter sur les nouveaux services
		//alert("Nom_Hote="+Nom_Hote);
		supprime_hote_temp(Nom_Hote);
*/		
		$("#"+parent).remove(); // suppression effective du fieldset.
		//alert("remove");
	};
//	};
};

var Doublon = ""; // déclaration globale pour permettre l'appel à une fonction distante
function supprime_fieldset_service(champ) 
{
	timer_enregistrement();
	controle_doublon();
	if (Doublon != "Oui")
	{
		var parent=$(champ).parent().parent().attr("id");
		var MessageConfirmation = "Vous allez supprimer le " + parent + " . Etes-vous sûr?";
		if (confirm(MessageConfirmation)) 
		{
			var Nom_Service = $("#Nom_"+parent+"").val(); //récupère le nom du service
			var Service_Hote = $("#Hote_"+parent+"").val(); //récupère le nom de l'hôte lié au service
			var nbdisabled_service = $('input:disabled[name=selection_service]').size(); // récupère le nombre de service désactivés
			if (nbdisabled_service > 0) // ne rentre dans la boucle que si des services sont désactivés
			{
				var tableau_service_lig = document.getElementById("T_Liste_Service").rows; //charge les lignes du tableau
				var NbLigne_Service = tableau_service_lig.length; // récupération du nombre d'enregistrement
				var Hote_ID = "";
				for (var i=1;i<NbLigne_Service;i++)
				{
					var T_service_col = tableau_service_lig[i].cells; //charge les cellules de chaque ligne désactivée dans un tableau
					
					if (T_service_col[1].innerHTML == Service_Hote)
					{
						Hote_ID = T_service_col[7].innerHTML;
					};
					$('input:disabled[id=s' + i +']').each(function() // on boucle sur chaque objet sélectionné
					{
						var tableau_service_col = tableau_service_lig[i].cells; //charge les cellules de chaque ligne désactivée dans un tableau
						if (tableau_service_col[2].innerHTML+tableau_service_col[7].innerHTML == Nom_Service+Hote_ID)
						{
							$(this).removeAttr("Disabled"); // réactive le service inséré pour permettre un nouvel ajout.
						};
					});
				};
			};
			// on supprime de la table service
			supprime_service(Nom_Service,Service_Hote);
			$("#"+parent).remove();  // supprime le fieldset de l'affichage
		};
	};
};

function supprime_service(nom_service,service_hote)
{
	var nom_service = encodeURIComponent(nom_service);
	var service_hote = encodeURIComponent(service_hote);
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			return xhr.responseText;
		};
	};
	xhr.open("POST", "supprime_service.php", false);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("nom_service="+nom_service+"&service_hote="+service_hote+"");
};

function supprime_fieldset_Plage(champ) 
{
	timer_enregistrement();
	var parent_plage=$(champ).parent().parent().parent().attr("id");
	var NbFieldset_plage = $("fieldset.plage").length; // encodage inutile puisqu'il n'y a pas de transmission d'information à une autre page.
/*	if ( NbFieldset_plage == 1 )
	{
		var Message = "Il n'y a qu'une seule plage horaire, vous ne pouvez pas la supprimer!";
		alert(Message);
	} else 
	{
*/
		var MessageConfirmation = "Vous allez supprimer la " + parent_plage + " . Etes-vous sûr?";
		if (confirm(MessageConfirmation)) 
		{
			var Nom_Plage = $('input[id=Nom_'+parent_plage+']').val();
			var nbdisabled_plage = $('input:disabled[name=selection_plage]').size(); // récupère le nombre de plages désactivés
			if (nbdisabled_plage > 0) // ne rentre dans la boucle que si des plages sont désactivés
			{
				//var Nom_Plage = $("#Nom_"+parent_plage+"").val();
				// gestion des plages importés pour réactivation dans la liste
				var tableau_plage_lig = document.getElementById("T_Liste_Plage").rows; //charge les lignes du tableau
				var NbLigne_Plage = tableau_plage_lig.length; // récupération du nombre d'enregistrement
				for (var i=1;i<NbLigne_Plage;i++)
				{
					$('input:disabled[id=p' + i +']').each(function() // on boucle sur chaque objet sélectionné
					{
						var tableau_plage_col = tableau_plage_lig[i].cells; //charge les cellules de chaque ligne désactivée dans un tableau
						//alert("TableauPlage="+tableau_plage_col[1].innerHTML);
						if (tableau_plage_col[1].innerHTML == Nom_Plage)
						{
							$(this).removeAttr("Disabled"); // réactive la plage inséré pour permettre un nouvel ajout.
						};
					});
				};
			};
//code désactivé car on doit garder la plage utilisable pour les services même si on ne la change pas
// cas à gérer si c'est un nouvel ajout (bouton PReenregistrer visible)
/*			// on supprime des listes plage_service l'enregistrement
			$("select[id*='Service_Plage']").each(function()
			{
				s_id_plage=$(this).attr("id");
				//alert("s_id_plage="+s_id_plage);
				//alert("Nom_Plage=" + Nom_Plage);
				$("#"+s_id_plage+" option[value='"+Nom_Plage+"']").remove(); // on supprime de chaque liste déroulante la plage
			});

			// on supprime de la table periode_temporelle pour ne plus l'ajouter sur les nouveaux services
			//alert("Nom_Plage="+Nom_Plage);
			supprime_periode(Nom_Plage);
*/

			$("#"+parent_plage).remove(); // supprime le fieldset de l'affichage
		};
//	};
};
/**
 * Fonction désactivée car nécessié de conserver la période pour les autres services. cf ci-dessus
function supprime_periode(nom_periode)
{
	var nom_periode = encodeURIComponent(nom_periode);
	//alert("nom_periode="+nom_periode);
	var xhr = getXMLHttpRequest(); //création de l'instance XHR
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
		{
			return xhr.responseText;
		};
	};
	xhr.open("POST", "supprime_periode.php", false);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
	xhr.send("nom_periode="+nom_periode+"");
};
*/
function clone_fieldset_Plage(champ) {
	var parent=$(champ).parent().parent().parent().attr("id");
	var NbFieldset = $("fieldset.plage").length;
	var liste_plage = "";
// 06/10/15 confirmation de duplication supprimée 
//	var MessageConfirmation = "Vous allez dupliquer la " + parent + " . Etes-vous sûr?";
//	if (confirm(MessageConfirmation)) 
//	{
		/**
		 *  boucler sur l'ensemble des champs de la plage
		 */
		$("." + parent.toLowerCase() + "").each(function()
		{
			/**
			 *  gestion des caractères spéciaux ! $ et | dans les champs.
			 */
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			/**
			 *  on prend tous les champs du formulaire sans exception
			 */
				liste_plage += "|" + Valeur_Champ;
		});
		liste_plage += "$";

		liste_plage = liste_plage.substring(1,liste_plage.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		liste_plage = liste_plage.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
		
		/**
		 *  calcul la valeur du nouvel plage
		 *  compter le nombre total de fieldset  
		 */
		var trou=false;
		/**
		 *  boucler sur chaque fieldset pour identifier les trous
		 */
		queryAll = document.querySelectorAll('.hote');
		for (var i = 0; i< NbFieldset; i++){
			var h = i+1;
			if (( "Plage"+ h != queryAll[i].id) && (!trou)) {
				/**
				 *  Ajouter le fieldset dans le premier trou trouvé
				 */
				var numtrou = i;
				var plagetrou = "Plage"+h;
				var premierplage = queryAll[i].id; // on stocke la premiere plage trouvé après le trou
				trou=true; 
				break;
			};
		};
		if (!trou){ // pas de trou dans la liste
			numtrou = NbFieldset;
			premierplage = "Plage"+numtrou;
		};

		/**
		 *  Appelle la fonction d'ajout d'une plage
		 */
		ajoute_fieldset_plage();
		numtrou++;
		var numplage=numtrou;
		/**
		 *  rempli les champs
		 */
		T_liste_plage = liste_plage.split("|");
		var i = 0;
		$(".plage" + numplage + "").each(function()
		{
			if (T_liste_plage[i-1] == "Autre")
			{
				$(this).parent().removeAttr("style");
			};
			if (T_liste_plage[i] == "Modifier")
			{
				$(this).val("Creer")
			} else
			{
				$(this).val(T_liste_plage[i]);
			};
			i++;
		});
// 06/10/15 confirmation de duplication supprimée
//	};
	alert("La plage n°" + numplage + " a été ajoutée.");
};

function clone_fieldset_hote(champ) {
	var parent=$(champ).parent().parent().attr("id");
	var NbFieldset = $("fieldset.hote").length;
	var liste_hote = "";
// 06/10/15 confirmation de duplication supprimée
//	var MessageConfirmation = "Vous allez dupliquer l'" + parent + " . Etes-vous sûr?";
//	if (confirm(MessageConfirmation)) 
//	{
		/**
		 *  boucler sur l'ensemble des champs de l'hote
		 */
		$("." + parent.toLowerCase() + "").each(function()
		{
			/**
			 *  gestion des caractères spéciaux ! $ et | dans les champs.
			 */
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			/**
			 *  on prend tous les champs du formulaire sans exception
			 */
				liste_hote += "|" + Valeur_Champ;
		});
		liste_hote += "$";

		liste_hote = liste_hote.substring(1,liste_hote.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		liste_hote = liste_hote.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
		
		/**
		 *  calcul la valeur du nouvel hote
		 *  compter le nombre total de fieldset
		 */
		var trou=false;
		/**
		 *  boucler sur chaque fieldset pour identifier les trous
		 */
		queryAll = document.querySelectorAll('.hote');
		for (var i = 0; i< NbFieldset; i++){
			var h = i+1;
			if (( "Hote"+ h != queryAll[i].id) && (!trou)) {
				/**
				 *  Ajouter le fieldset dans le premier trou trouvé
				 */
				var numtrou = i;
				var hotetrou = "Hote"+h;
				var premierhote = queryAll[i].id; // on stocke le premier hote trouvé après le trou
				trou=true; 
				break;
			};
		};
		if (!trou){ // pas de trou dans la liste
			numtrou = NbFieldset;
			premierhote = "Hote"+numtrou;
		}

		/**
		 *  Appelle la fonction d'ajout d'un hôte
		 */
		ajoute_fieldset_hote();
		numtrou++;
		var numhote=numtrou;
		/**
		 *  rempli les champs
		 */
		T_liste_hote = liste_hote.split("|");
		var i = 0;
		$(".hote" + numhote + "").each(function()
		{
			if (T_liste_hote[i-1] == "Autre")
			{
				$(this).parent().removeAttr("style");
			};
			if ((T_liste_hote[i] == "Modifier") || (T_liste_hote[i] == "Desactiver") || (T_liste_hote[i] == "Supprimer"))
			{
				$(this).val("Creer")
			} else
			{
				$(this).val(T_liste_hote[i]);
			};
			i++;
		});
// 06/10/15 confirmation de duplication supprimée
//	};
	alert("L'hôte n°" + numhote + " a été ajouté.");
};

function clone_fieldset_service(champ) {
	var parent=$(champ).parent().parent().attr("id");
	var NbFieldset = $("fieldset.service").length;
	var liste_service = "";
// 06/10/15 confirmation de duplication supprimée
//	var MessageConfirmation = "Vous allez dupliquer le " + parent + ". Etes-vous sûr?";
//	if (confirm(MessageConfirmation)) 
//	{
		/**
		 *  boucler sur l'ensemble des champs de le service
		 */
		$("." + parent.toLowerCase() + "").each(function()
		{
			/**
			 *  gestion des caractères spéciaux ! $ et | dans les champs.
			 */
			var Valeur_Champ =  $(this).val();
			Gestion_caractere_speciaux(Valeur_Champ);
			/**
			 *  on prend tous les champs du formulaire sans exception
			 */
			liste_service += "|" + Valeur_Champ;
		});
		
		var liste_service_Arg = ""; // cette liste est réinitialise pour chaque fieldset service
		$(".Service_Argument" + parent.substring(7) + "").each(function()
		{
			var Valeur_Champ =  $(this).val();
			/**
			 *  gestion du "!" en tant que caractère de l'argument afin qu'il ne soit pas considéré comme un séparateur d'argument pour l'affichage plus bas.
			 */
			var reg1=new RegExp("[!]","g");
			if (Valeur_Champ.match(reg1))
			{
				Valeur_Champ = Valeur_Champ.replace(/!/g,"_PEX_");
			};
			liste_service_Arg += "!" + Valeur_Champ;
		});
		liste_service += "|" + liste_service_Arg.substring(1); // on enlève le premier | des arguments
		liste_service += "$";

		liste_service = liste_service.substring(1,liste_service.length-1).replace(/\$\|/g,"$"); // enlève le premier "|" et remplace les "$|" par un simple "$"
		liste_service = liste_service.replace(/\$\$/g,"$"); // remplace les "$$" par un simple "$"
		
		/**
		 *  calcul la valeur du nouveau service
		 *  compter le nombre total de fieldset 
		 */
		var trou=false;
		/**
		 *  boucler sur chaque fieldset pour identifier les trous
		 */
		queryAll = document.querySelectorAll('.service');
		for (var i = 0; i< NbFieldset; i++){
			var h = i+1;
			if (( "Service"+ h != queryAll[i].id) && (!trou)) {
				/**
				 *  Ajouter le fieldset dans le premier trou trouvé
				 */
				var numtrou = i;
				var servicetrou = "Service"+h;
				var premierservice = queryAll[i].id; // on stocke le premier service trouvé après le trou
				trou=true; 
				break;
			};
		};
		if (!trou){ // pas de trou dans la liste
			numtrou = NbFieldset;
			premierservice = "Service"+numtrou;
		};

		/**
		 *  Appelle la fonction d'ajout d'un service
		 */
		ajoute_fieldset_service();
		numtrou++;
		var numservice=numtrou;
		/**
		 *  rempli les champs
		 */
		T_liste_service = liste_service.split("|");
		var NbService = T_liste_service.length;
		NbService--;
		var liste_arg= T_liste_service[NbService];
		var i = 0;
		$(".service" + numservice + "").each(function()
		{
			if ((T_liste_service[i] == "Modifier") || (T_liste_service[i] == "Desactiver") || (T_liste_service[i] == "Supprimer")) 
			{
				$(this).val("Creer")
			} else
			{
				$(this).val(T_liste_service[i]);
			};
			if ($(this).attr("id") == "Service_Modele"+numservice)
			{
				/**
				 * changement le 09/02/15 suite ajout fonction Sauve_argument
				 */
				afficher_argument(numservice,liste_arg);
			};
			i++;
		});
//  06/10/15 confirmation de duplication supprimée
//	};
	alert("Le service n°" + numservice + " a été ajouté.");
};

function charger_liste_recherche_hote()
{
	var Client = document.getElementById('clientsup').options[document.getElementById('clientsup').selectedIndex].value;
	var Search_Hote = document.getElementById('recherche_hote').value;
	function requete_recherche_hote(callback) 
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
				callback(xhr.responseText); // C'est bon \o/
			};
		};
		var sClient = encodeURIComponent(Client);
		var sSearch_Hote = encodeURIComponent(Search_Hote);
		xhr.open("POST", "requete_liste_recherche_hote.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("sSearch_Hote="+sSearch_Hote+"&sClient="+sClient+""); 
	};
	function readData_recherche_hote(liste_recherche_hote) 
	{
		$("#liste_recherche_hote").empty(); // purge la liste à chaque nouvelle sélection d'hôte
		$("#liste_recherche_hote").append(liste_recherche_hote); // rempli la liste avec la sélection courante
		$("#Ajouter_Selection_Hote").removeAttr("Disabled");
	};
	requete_recherche_hote(readData_recherche_hote);
};

function deverouille_liste(champ)
{
	$("#"+champ.id.substring(4)).removeAttr("Disabled"); // on réactive la liste déroulante
	$("#"+champ.id.substring(4)).removeAttr("readOnly"); // on réactive la liste déroulante
	$("#"+champ.id).attr("alt","incorrect"); // on repositionne les attributs de l'icône
	$("#"+champ.id).attr("src","images/img_edit.png"); // on repositionne les attributs de l'icône
	if ($("#"+champ.id).parent().attr("id") == "model_param_plage"){// si deverrouille plage => Suppression action OK, ne reste que "A Modifier"
		var class_plage=$(champ).parent().parent().attr("id").toLowerCase();
		$("."+class_plage+" option").remove();
		$("select."+class_plage+"").append('<option value="Modifier">A Modifier</option>');
	};
};

function change_statut(champ)
{
	/**
	 * fonction de modification de validation des champs obligatoires
	 * en fonction de l'action à effectuer
	 * 	- si désactivation ou suppression => force champs à "correct"
	 *  - si activation ou modification => force champs à "incorrect"
	 */
	// récupérer l'id et la valeur sélectionnée
	var id_champ=$(champ).attr("id");
	var valeur_champ=document.getElementById(champ.id).options[document.getElementById(champ.id).selectedIndex];
//	alert("id_champ="+ id_champ);
//	alert("valeur_champ="+ valeur_champ.value);
	if (id_champ.substring(0,7) == "Service") // s'il s'agit d'un service
	{
		var numservice = id_champ.substring(14);
//		alert("numservice="+numservice);
		$("[id*='img_Service_Argument" + numservice + "']").each(function() // pour chaque champ argument à valider
		{
			if ((valeur_champ.value == "Desactiver") ||(valeur_champ.value == "Supprimer")) // si la sélection est supprimer ou désactiver
			{
				$(this).attr("alt","correct");
				$(this).attr("src","images/img_ok.png");
				$("#img_Consigne_Service_Detail"+numservice).attr("alt","correct");
				$("#img_Consigne_Service_Detail"+numservice).attr("src","images/img_ok.png");
			} else // si la sélection est activer ou modifier
			{
				$(this).attr("alt","incorrect");
				$(this).attr("src","images/img_edit.png");
				$("#img_Consigne_Service_Detail"+numservice).attr("alt","incorrect");
				$("#img_Consigne_Service_Detail"+numservice).attr("src","images/img_edit.png");
			};
		});
	} else if (id_champ.substring(0,4) == "Hote") // s'il s'agit d'un hote
	{
		var numhote = id_champ.substring(11);
//		alert("numservice="+numservice);
//		$("[id*='img_Hote_Argument" + numhote + "']").each(function() // pour chaque champ argument à valider
//		{
			if ((valeur_champ.value == "Desactiver") ||(valeur_champ.value == "Supprimer")) // si la sélection est supprimer ou désactiver
			{
				$("#img_IP_Hote"+numhote).attr("alt","correct");
				$("#img_IP_Hote"+numhote).attr("src","images/img_ok.png");
				$("#img_Hote_Description"+numhote).attr("alt","correct");
				$("#img_Hote_Description"+numhote).attr("src","images/img_ok.png");
				$("#img_Localisation"+numhote).attr("alt","correct");
				$("#img_Localisation"+numhote).attr("src","images/img_ok.png");
				$("#img_Type_Hote"+numhote).attr("alt","correct");
				$("#img_Type_Hote"+numhote).attr("src","images/img_ok.png");
				$("#img_Type_OS"+numhote).attr("alt","correct");
				$("#img_Type_OS"+numhote).attr("src","images/img_ok.png");
				$("#img_Architecture"+numhote).attr("alt","correct");
				$("#img_Architecture"+numhote).attr("src","images/img_ok.png");
				$("#img_Langue"+numhote).attr("alt","correct");
				$("#img_Langue"+numhote).attr("src","images/img_ok.png");
				$("#img_Consigne_Hote_Detail"+numhote).attr("alt","correct");
				$("#img_Consigne_Hote_Detail"+numhote).attr("src","images/img_ok.png");
			} else // si la sélection est activer ou modifier
			{
				$("#img_IP_Hote"+numhote).attr("alt","incorrect");
				$("#img_IP_Hote"+numhote).attr("src","images/img_edit.png");
				$("#IP_Hote"+numhote).removeAttr("Disabled");
				$("#IP_Hote"+numhote).removeAttr("readOnly");
				$("#img_Hote_Description"+numhote).attr("alt","incorrect");
				$("#img_Hote_Description"+numhote).attr("src","images/img_edit.png");
				$("#Hote_Description"+numhote).removeAttr("Disabled");
				$("#Hote_Description"+numhote).removeAttr("readOnly");
				$("#img_Localisation"+numhote).attr("alt","incorrect");
				$("#img_Localisation"+numhote).attr("src","images/img_edit.png");
				$("#Localisation"+numhote).removeAttr("Disabled");
				$("#Localisation"+numhote).removeAttr("readOnly");
				$("#img_Type_Hote"+numhote).attr("alt","incorrect");
				$("#img_Type_Hote"+numhote).attr("src","images/img_edit.png");
				$("#Type_Hote"+numhote).removeAttr("Disabled");
				$("#Type_Hote"+numhote).removeAttr("readOnly");
				$("#img_Type_OS"+numhote).attr("alt","incorrect");
				$("#img_Type_OS"+numhote).attr("src","images/img_edit.png");
				$("#Type_OS"+numhote).removeAttr("Disabled");
				$("#Type_OS"+numhote).removeAttr("readOnly");
				$("#img_Architecture"+numhote).attr("alt","incorrect");
				$("#img_Architecture"+numhote).attr("src","images/img_edit.png");
				$("#Architecture"+numhote).removeAttr("Disabled");
				$("#Architecture"+numhote).removeAttr("readOnly");
				$("#img_Langue"+numhote).attr("alt","incorrect");
				$("#img_Langue"+numhote).attr("src","images/img_edit.png");
				$("#Langue"+numhote).removeAttr("Disabled");
				$("#Langue"+numhote).removeAttr("readOnly");
				$("#img_Consigne_Hote_Detail"+numhote).attr("alt","incorrect");
				$("#img_Consigne_Hote_Detail"+numhote).attr("src","images/img_edit.png");
			};
//		});
	};
////	$(".service" + numservice + "").each(function()
////	{
//	$("#"+champ.id.substring(4)).removeAttr("Disabled"); // on réactive la liste déroulante
//	$("#"+champ.id.substring(4)).removeAttr("readOnly"); // on réactive la liste déroulante
//	$("#"+champ.id).attr("alt","incorrect"); // on repositionne les attributs de l'icône
//	$("#"+champ.id).attr("src","images/img_edit.png"); // on repositionne les attributs de l'icône
//	if ($("#"+champ.id).parent().attr("id") == "model_param_plage"){// si deverrouille plage => Suppression action OK, ne reste que "A Modifier"
//		var class_plage=$(champ).parent().parent().attr("id").toLowerCase();
//		$("."+class_plage+" option").remove();
//		$("select."+class_plage+"").append('<option value="Modifier">A Modifier</option>');
//	};
};

/* inutilisé le 21/10/2014
function deverouille_etat(champ)
{
	alert("test");
	$("#"+champ.id).removeAttr("Disabled"); // on réactive la liste déroulante
	$("#"+champ.id.substring(6)).removeAttr("Disabled"); // on réactive le bouton de MAJ
	//$("#"+champ.id).attr("alt","incorrect"); // on repositionne les attributs de l'icône
	//$("#"+champ.id).attr("src","images/img_edit.png"); // on repositionne les attributs de l'icône
//	if ($("#"+champ.id).parent().attr("id") == "model_param_plage")// si deverrouille plage => Suppression action OK, ne reste que "A Modifier"
//		var class_plage=$(champ).parent().parent().attr("id").toLowerCase();
//		$("."+class_plage+" option").remove();
//		$("select."+class_plage+"").append('<option value="Modifier">A Modifier</option>');
};
*/

function sauve_argument(Num_Service_Modele,Nom_Modele)
{
	//var Num_Service_Modele
	var fieldset_existe = $("#Ancien_Arg_Service_Modele"+Num_Service_Modele);
	if (! fieldset_existe.length) // si le fieldset n'existe pas (sa taille est vide)
	{
		//alert(Num_Service_Modele);
		//alert(Nom_Modele);
		$("#Arg_Service_Modele"+Num_Service_Modele).clone().insertAfter("#Arg_Service_Modele"+Num_Service_Modele);
		//$("#Arg_Service_Modele"+Num_Service_Modele).next().css("background-color","#000000");
		$("#Arg_Service_Modele"+Num_Service_Modele).next().attr("id","Ancien_Arg_Service_Modele"+Num_Service_Modele);
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele + " legend").text("Anciens arguments du modèle [" + Nom_Modele + "]"); // modifie la légende du nouveau fieldset
		//$("#Ancien_Arg_Service_Modele"+Num_Service_Modele).children().css("background-color","#E70739");
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele).children().remove("img");
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele).children().attr("readOnly","readOnly");
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele).children().removeAttr("onblur");
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele).children().removeAttr("class");
		$("#Ancien_Arg_Service_Modele"+Num_Service_Modele + " legend").after("<p class='attention'>Attention ces informations seront perdues après enregistrement et rechargement de la page!</p>");
	};	
	
	
};

function afficher_argument(Num_Service_Modele,liste_arg)
{
	/**
	 * liste_arg transmise par fonction clone_fieldset_service uniquement
	 */
	var Service_Modele = "Service_Modele" + Num_Service_Modele;
	//alert("Service_Modele="+Service_Modele);
	//alert("liste_arg="+liste_arg);
	if (! liste_arg)
	{
		var liste_arg = "";
	}
	var T_liste_argument = liste_arg.split("!");
	var Modele = document.getElementById(Service_Modele).options[document.getElementById(Service_Modele).selectedIndex].value;
	var Modele_id = Service_Modele; // Correspond à l'ID HTML du modèle de service sélectionné
	function requete_Service_Argument(callback) 
	{
		var xhr = getXMLHttpRequest(); //création de l'instance XHR
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
			{
				//alert(xhr.responseText);
				callback(xhr.responseText); // C'est bon \o/
				if (T_liste_argument[0] != "" && liste_arg != "") // si une liste d'argument est transmise uniquement
				{
					var j = 0;
//					$(".Service_Argument" + numservice + "").each(function()
					$(".Service_Argument" + Num_Service_Modele + "").each(function()
					{
						//alert("argument courant="+T_liste_argument[j]);
						// gestion "!" en tant que caractère de l'argument et pas comme séparateur
						var reg1=new RegExp("[_PEX_]","g");
						if (T_liste_argument[j].match(reg1))
						{
							T_liste_argument[j] = T_liste_argument[j].replace(/_PEX_/g,"!");
						};
						//$(this).val(decodeURI(T_liste_argument[j])); inutile de décoder puisque récupéraion directe de l'interface
						$(this).val(T_liste_argument[j]);
						j++;
					});
//					alert("Le service n°" + numservice + " a été ajouté.");
					alert("Le service n°" + Num_Service_Modele + " a été ajouté.");
				};
			};
		};
		var sModele = encodeURIComponent(Modele);
		var sModele_id = encodeURIComponent(Modele_id);
		//alert("sModele="+sModele);
//		var sSearch_Hote = encodeURIComponent(Search_Hote);
		xhr.open("POST", "requete_liste_Service_Argument.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
		xhr.send("sModele="+sModele+"&sModele_id="+sModele_id+""); 
	};
	function readData_Service_Argument(liste_Service_Argument) 
	{
		//alert("Modele_id="+Modele_id);
		//alert("liste_Service_Argument="+liste_Service_Argument);
		//alert("Modele_id=Arg_"+Modele_id);
		//$("Arg_"+Modele_id).clone().appendTo("Arg_"+Modele_id"); 
		$("#Arg_"+Modele_id).empty(); // purge la liste à chaque nouvelle sélection de modele
		$("#Arg_"+Modele_id).append(liste_Service_Argument); // rempli la liste avec la sélection courante
//		$("#Arg_"+Modele.id+"").append(<p>test</p>); // rempli la liste avec la sélection courante
//		$("fieldset[id='Inactif_Arg_"+Modele_id+"']").removeAttr("style");
//		$("fieldset[id='Inactif_Arg_"+Modele_id+"']").children("img").remove();
//		$("fieldset[id='Inactif_Arg_"+Modele_id+"']").children("input").removeAttr('onblur');
//		$("fieldset[id='Inactif_Arg_"+Modele_id+"']").children("input").removeAttr('class');
//		$("fieldset[id='Inactif_Arg_"+Modele_id+"']").children("input").attr('readOnly','readonly');
		//alert("maj effectuée");
	};
	requete_Service_Argument(readData_Service_Argument);
	//return true;
};


function valider_extraction()
{
	/**
	 *  fonction dédiée à l'extraction d'une prestation
	 */
	var ma_selection= document.getElementById('prestation').options[document.getElementById('prestation').selectedIndex];
	if(ma_selection.value =="")
	{
		// purge la liste à chaque nouvelle sélection de prestation
		$("#extraction_elements").empty();
		//$("#extraction_hote").empty();
		//$("#extraction_service").empty();
        //$("#extraction_plage").empty();
	} else
	{
		// purge la liste à chaque nouvelle sélection de prestation
		$("#extraction_elements").empty();
		//$("#extraction_hote").empty();
		//$("#extraction_service").empty();
        //$("#extraction_plage").empty();
		var sPrestation = encodeURIComponent(ma_selection.value);

		function requete_elements(callback)
 		{
			var xhr_e = getXMLHttpRequest(); //création de l'instance XHR
			var loading=false;
			xhr_e.onreadystatechange = function()
			{
				if (xhr_e.readyState == 4 && (xhr_e.status == 200 || xhr_e.status == 0))
				{
					$("#e_img_loading").remove();
					$("#e_p_loading").remove();
					callback(xhr_e.responseText); // C'est bon \o/
				} else if(xhr_e.readyState == 4 && xhr_e.status != 200) { // En cas d'erreur !
					$("#e_img_loading").remove();
					$("#e_p_loading").remove();
					gestion_erreur(xhr_e);
				} else if (loading == false){
					loading=true;
					$("#extraction_elements").removeAttr("style");
					//$("fieldset#extraction_elements").append('<legend>Liste des éléments</legend>');
					$("fieldset#extraction_elements").append('<img id="e_img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant la recherche des éléments..." style="vertical-align:middle;isplay:inline;"/>');
					$("fieldset#extraction_elements").append('<p id="e_p_loading">Veuillez patienter pendant la recherche des éléments...</p>');
				};
			};
			//var sPrestation = encodeURIComponent(ma_selection.value);
			xhr_e.open("POST", "extraction_elements_html.php", true);
			xhr_e.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
			//xhr_e.send("prestation="+sPrestation+"&PDF=Non"); 
			xhr_e.send("prestation="+sPrestation+"");
		};
		function readData_elements(extraction_elements)
		{
			$("#extraction_elements").empty(); // purge la liste à chaque nouvelle sélection de prestation
			$("#extraction_elements").append(extraction_elements); // rempli la liste avec la sélection courante
		};
		
		requete_elements(readData_elements);
	};
};

function valider_extraction_pdf()
{
	var ma_selection= document.getElementById('prestation').options[document.getElementById('prestation').selectedIndex];
	if(ma_selection.value =="")
	{
		// purge la liste à chaque nouvelle sélection de prestation
		$("#extraction_elements").empty();
	} else
	{
		// purge la liste à chaque nouvelle sélection de prestation
		$("#extraction_elements").empty();
		var sPrestation = encodeURIComponent(ma_selection.value);
		function requete_elements(callback)
 		{
			var xhr_e = getXMLHttpRequest(); //création de l'instance XHR
			var loading=false;
			xhr_e.onreadystatechange = function()
			{
				if (xhr_e.readyState == 4 && (xhr_e.status == 200 || xhr_e.status == 0))
				{
					$("#e_img_loading").remove();
					$("#e_p_loading").remove();
					callback(xhr_e.responseText); // C'est bon \o/
				} else if(xhr_e.readyState == 4 && xhr_e.status != 200)
				{ // En cas d'erreur !
					$("#e_img_loading").remove();
					$("#e_p_loading").remove();
					gestion_erreur(xhr_e);
				} else if (loading == false){
					loading=true;
					$("#extraction_elements").append('<p id="e_p_loading">Veuillez patienter pendant le chargement des éléments et la construction du PDF...</p>');
					$("#extraction_elements").append('<img id="e_img_loading" src="images/chargement.gif" alt="Veuillez patienter pendant le chargement des éléments..."/> ');

				};
			};

			//var sPrestation = encodeURIComponent(ma_selection.value);
			xhr_e.open("POST", "extraction_elements_pdf.php", true);
			//xhr_e.open("POST", "extraction_elements_pdf.php", true);
			xhr_e.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // nécessaire avec la méthode POST sinon le serveur ignore la requête
//			xhr_e.send("prestation="+sPrestation+"&PDF=Oui"); 
			xhr_e.send("prestation="+sPrestation+""); 
		};

		function readData_elements(extraction_elements)
		{
			$("#extraction_elements").empty(); // purge la liste à chaque nouvelle sélection de prestation
			$("#extraction_elements").append(extraction_elements); // rempli la liste avec la sélection courante
		};
		requete_elements(readData_elements);
	};
};