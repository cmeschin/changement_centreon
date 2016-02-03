function surligne(img, erreur)
{
	if(erreur)
	{
		img.src = "images/img_ko.png";
		img.alt = "incorrect";
	}
	else
	{
		img.src = "images/img_ok.png";
		img.alt = "correct";
	}
}
function verifChamp(champ)
{
	champ.value=champ.value.replace(/^\s$/,'NC'); //remplace les champs ne contenant qu'un espace par NC
	champ.value=champ.value.trim(); //supprime les espaces devant et derrière
	if(champ.value.length < 1 )
	{
		surligne (document.getElementById("img_"+champ.id), true);
		return false;
	}
	else
	{
		surligne (document.getElementById("img_"+champ.id), false);
		return true;
	}
}

function majuscule(champ)
{
	champ.value=champ.value.toUpperCase();
}

function minuscule(champ)
{
	champ.value=champ.value.toLowerCase();
}

function verifNom_Hote(champ)
{
	//alert(champ.value);
	var reg=/[^-_^a-z^0-9]/i; // exclu les caractères normaux du contrôle
	if (reg.exec(champ.value)!=null)
	{
		surligne (document.getElementById("img_"+champ.id), true);
		alert("Le Nom de l'hôte ne doit contenir que des caractères alphanumériques non accentués et les tirets haut (-) et bas (_) uniquement. Merci de corriger.");
		return false;
	} else
	{
		surligne (document.getElementById("img_"+champ.id), false);
		return true;
	};
}

function verifNom_Service(champ)
{
	//alert(champ.value);
	champ.value=champ.value.trim(); //supprime les espaces devant et derrière
	champ.value=champ.value.replace(/\s{2,}/g,' '); // remplace tous les doubles espaces par des simples espaces
	//champ.value=champ.value.replace(/ /g,'_'); // remplace tous les espaces par des _
	var reg=/[^-_:.^a-z^0-9#@/\ ]/i; // exclu les caractères normaux du contrôle
	if (reg.exec(champ.value)!=null)
	{
		surligne (document.getElementById("img_"+champ.id), true);
		alert("Le Nom du service contient des caractères accentués ou spéciaux non autorisés. Merci de corriger.");
		return false;
	} else
	{
		surligne (document.getElementById("img_"+champ.id), false);
		return true;
	};
}

function verifChampIP(champ)
{
var reg=/^\d{1,3}[.]\d{1,3}[.]\d{1,3}[.]\d{1,3}$/;
	if (reg.exec(champ.value)==null){
		surligne (document.getElementById("img_"+champ.id), true);
		alert("Veuillez saisir une adresse IP valide.");
		return false;
	}else{
		var tab=champ.value.split('.');
		var compterreur=0;
		for(i=0;i<4;i++){
			if ((tab[i]-'0')>255){
			compterreur++;
			}
		}
		if(compterreur==0){
			surligne (document.getElementById("img_"+champ.id), false);
			//alert("IP valide");
			return true;
		}else{
			surligne (document.getElementById("img_"+champ.id), true);
			alert("Veuillez saisir une adresse IP valide.");
			return false;
		}
	}
}

function verifChampNonIP(champ)
{
var reg=/^\d{1,3}[.]\d{1,3}[.]\d{1,3}[.]\d{1,3}$/;
	if (reg.exec(champ.value)==null){
		surligne (document.getElementById("img_"+champ.id), false);
		//alert("ce n'est pas une adresse IP, OK on continue...");
		//return true;
	}else{
		alert("Ce champ doit ne doit pas contenir d'adresse IP mais le nom de l'hôte.");
		surligne (document.getElementById("img_"+champ.id), true);
		return false;
	};
}

function verifChampMail(champ)
{
	//var liste_mail = champ.value.replace(/ /g,""); // supprime tous les espaces de la chaine
	var liste_mail = champ.value.trim().toLowerCase(); // supprime les espaces avant et après la chaine et force en minuscule
	var reg = /^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
	var T_mail = "";
	T_mail = liste_mail.split(';'); // on découpe la chaine en tableau sur la base du point-virgule
	var NbMail = T_mail.length;
	var compterreur=0;
	for(i=0;i<NbMail;i++)
	{
		if (reg.exec(T_mail[i].trim())==null && T_mail[i].trim()!= "")
		{
			alert("L'adresse mail [ "+ T_mail[i] +" ] est invalide!\nLes adresses mails doivent être séparées par un point-virgule (;)");
			compterreur++;
		};
	};
	if(champ.value.length < 1 )
	{
		surligne (document.getElementById("img_"+champ.id), true);
		return false;
	}
	else if(compterreur==0){
		surligne (document.getElementById("img_"+champ.id), false);
		//alert("Mail valide");
		return true;
	}else{
		surligne (document.getElementById("img_"+champ.id), true);
		//alert("Mail invalide");
		return false;
	};
	
};

function verifAll()
{
	var formulaire_ok = true;
	var imgs = document.getElementsByClassName("verif");

	for(var i=0; i<imgs.length; i++) {

		if(imgs[i].tagName == "IMG" && imgs[i].alt == "incorrect") {
			formulaire_ok = false;
		}
	}
	
	if(!formulaire_ok) {
		alert("Certains champs sont invalides. Merci de les corriger.");
		return false;
	} else {
		return true;
	}
}

function verifChampDate_date_livraison_demandee(champ,date_test)
{
//	sleep(500);
//	alert(date_test);
	if(date_test == "" ){
		surligne (document.getElementById("img_"+champ.id), true);
		return false;
	} else {
		surligne (document.getElementById("img_"+champ.id), false);
		return true;
	}
//	surligne (document.getElementById("img_date_livraison_demandee"), false);
//	return true;
}
