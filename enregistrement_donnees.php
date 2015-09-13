<?php
if (session_id()=='')
{
	session_start();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement enregistrement_Demande.php");

//include_once('connexion_sql_supervision.php'); // déporté sur enregistrement_brouillon.php et enregistrement_demande.php
$sinfo_gen = (isset($_POST["info_gen"])) ? $_POST["info_gen"] : NULL;
$sliste_hote = (isset($_POST["liste_hote"])) ? $_POST["liste_hote"] : NULL;
$sliste_service = (isset($_POST["liste_service"])) ? $_POST["liste_service"] : NULL;
$sliste_plage = (isset($_POST["liste_plage"])) ? $_POST["liste_plage"] : NULL;

$info_gen = explode("$",$sinfo_gen); // découpe la chaine en tableau avec comme séparateur le $
$liste_hote = explode("$",$sliste_hote); // découpe la chaine en tableau avec comme séparateur le $
$liste_service = explode("$",$sliste_service); // découpe la chaine en tableau avec comme séparateur le $
$liste_plage = explode("$",$sliste_plage); // découpe la chaine en tableau avec comme séparateur le $
// chaque tableau devra être redécoupé pour mise à jour unitaire

$ID_Demande = htmlspecialchars($_SESSION['ID_dem']);

if ($info_gen[0] != "")  // S'il y a des données ce qui doit forcément être la cas
{
	// Calcul du nombre d'hôte à mettre à jour
	//	$NbInfo = count($info_gen);
	//	addlog("NbInfo=".$NbInfo);
	//	for ($i = 0;$i<$NbInfo;$i++)
	//	{
	//		addlog("info_gen=".($info_gen[$i]));
	//		$info_gen = explode("|",$info_gen[$i]);
	addlog("Demandeur=".($info_gen[0]));
	addlog("Date_Demande=".($info_gen[1]));
	addlog("Etat_Demande=".($info_gen[2]));
	addlog("Ref_Demande=".($info_gen[3]));
	addlog("Code_Client=".($info_gen[4]));
	addlog("Date_supervision=".($info_gen[5]));
	addlog("email=".($info_gen[6]));
	addlog("Commentaire=".($info_gen[7]));

	//	0		1				2							3		4		5				6			7				8					9					10				11						12		13
	//	BEEWARE	#10.33.253.8	#boitier Beeware Secours	#BDX1	#SRV	#NC				#32_bits	#Francais		#Reverse Proxy		#commentaire		#lien			#deesc					#actif	#Desactiver
	
//		$Select_infos = $bdd_supervision->prepare('SELECT Ref_Demande FROM demande WHERE ID_Demande= :ID_Demande;');
//		$Select_infos->execute(array(
//			'Ref_Demande' => htmlspecialchars($info_gen[3]),
//			'ID_Demande' => $ID_Demande
//		)) or die(print_r($Select_infos->errorInfo()));
		
//	$UPDATE_infos=False;
//	$res_Select_infos = $Select_infos->fetchAll();
//	foreach($res_Select_infos as $Ref_Demande) {
//	addlog("Ref_Demande=".$Ref_Demande[3]);
//	$UPDATE_infos = True;
//	};

//	if ($UPDATE_infos==True)
//	{
	addlog("MAJ Table Demande");
	$MAJ_infos = $bdd_supervision->prepare('UPDATE demande 
		SET Date_Demande= :Date_Demande,
			Date_Supervision_Demandee= :Date_Supervision_Demandee,
			Commentaire= :Commentaire,
			email= :email
		WHERE ID_Demande= :ID_Demande;');
	$MAJ_infos->execute(array(
		'Date_Demande' => date("Y-m-d H:i:s"), // date de la demande mise à jour au moment de la validation de la demande
		'Date_Supervision_Demandee' => htmlspecialchars($info_gen[5]),
		'Commentaire' => htmlspecialchars($info_gen[7]),
		'email' => htmlspecialchars(strtolower($info_gen[6])), // forçage en minuscule pour la compatibilité des mails
		'ID_Demande' => $ID_Demande
	)) or die(print_r($MAJ_infos->errorInfo()));
//		} else
//		{
//				addlog("INSERT Table Demande");
//			$MAJ_infos = $bdd_supervision->prepare('INSERT INTO demande 
//				(Code_Client, Demandeur, Date_Demande, Ref_Demande, Date_Supervision_Demandee, Etat_Demande, Commentaire, email)
//				VALUES (:Code_Client, :Demandeur, :Date_Demande, :Ref_Demande, :Date_Supervision_Demandee, :Etat_Demande, :Commentaire, :email)');
//			$MAJ_hote->execute(array(
//				'Code_Client' => htmlspecialchars($info_gen[5]),
//				'Demandeur' => htmlspecialchars($info_gen[0]),
//				'Date_Demande' => htmlspecialchars($info_gen[1]),
//				'Date_Supervision_Demandee' => htmlspecialchars($info_gen[4]),
//				'Etat_Demande' => htmlspecialchars($info_gen[2]),
//				'Commentaire' => htmlspecialchars($info_gen[7]),
//				'email' => htmlspecialchars($info_gen[6]),
//				)) or die(print_r($MAJ_infos->errorInfo()));
//		};
//		addlog(print_r($MAJ_infos));
//	};
};

if ($liste_hote[0] != "")  // S'il y a au moins un hôte
{
	// Calcul du nombre d'hôte à mettre à jour
	$NbHote = count($liste_hote);
	addlog("NbHote=".$NbHote);
	for ($i = 0;$i<$NbHote;$i++)
	{
		addlog("liste_hote=".($liste_hote[$i]));
		$liste_T_hote = explode("|",$liste_hote[$i]);
		addlog("Nom_hote=".($liste_T_hote[0]));

		//	0		1				2							3		4		5				6			7				8					9					10				11						12		13
		//	BEEWARE	#10.33.253.8	#boitier Beeware Secours	#BDX1	#SRV	#NC				#32_bits	#Francais		#Reverse Proxy		#commentaire		#lien			#deesc					#actif	#Desactiver
		//	// Efface les hôtes de la demande avant réinsertion
		//	$DEL_Hote = $bdd_supervision->prepare('DELETE FROM hote WHERE ID_Demande= :ID_Demande;');
		//	$DEL_Hote->execute(array(
		//		'ID_Demande' => $ID_Demande
		//	)) or die(print_r($DEL_Hote->errorInfo()));
			addlog("INSERT Table Hote");
//			$MAJ_hote = $bdd_supervision->prepare('INSERT INTO hote 
//				(Nom_Hote, ID_Demande, Description, IP_Hote, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne, Detail_Consigne, Type_Action)
//				VALUES (:Nom_Hote, :ID_Demande, :Description, :IP_Hote, :Type_Hote, :ID_Localisation, :OS, :Architecture, :Langue, :Fonction, :Controle_Actif, :Commentaire, :Consigne, :Detail_Consigne, :Type_Action)');
			$MAJ_hote = $bdd_supervision->prepare('INSERT INTO hote 
				(Nom_Hote, ID_Demande, Description, IP_Hote, Type_Hote, ID_Localisation, OS, Architecture, Langue, Fonction, Controle_Actif, Commentaire, Consigne, Detail_Consigne, Type_Action)
				VALUES (:Nom_Hote, :ID_Demande, :Description, :IP_Hote, :Type_Hote, :ID_Localisation, :OS, :Architecture, :Langue, :Fonction, :Controle_Actif, :Commentaire, :Consigne, :Detail_Consigne, :Type_Action)
				ON DUPLICATE KEY UPDATE Nom_Hote= :nom_hote2, ID_Demande= :id_demande2, Description= :description2, IP_Hote= :ip_hote2, Type_Hote= :type_hote2, ID_Localisation= :id_localisation2, OS= :os2, Architecture= :architecture2, Langue= :langue2, Fonction= :fonction2, Controle_Actif= :controle_actif2, Commentaire= :commentaire2, Consigne= :consigne2, Detail_Consigne= :detail_consigne2, Type_Action= :type_action2');
			$MAJ_hote->execute(array(
				'Nom_Hote' => htmlspecialchars($liste_T_hote[0]),
				'ID_Demande' => $ID_Demande,
				'Description' => htmlspecialchars($liste_T_hote[2]),
				'IP_Hote' => htmlspecialchars($liste_T_hote[1]),
				'Type_Hote' => htmlspecialchars($liste_T_hote[4]),
				'ID_Localisation' => htmlspecialchars($liste_T_hote[3]),
				'OS' => htmlspecialchars($liste_T_hote[5]), // récupérer la valeur texte pour affichage
				'Architecture' => htmlspecialchars($liste_T_hote[6]),
				'Langue' => htmlspecialchars($liste_T_hote[7]),
				'Fonction' => htmlspecialchars($liste_T_hote[8]),
				'Controle_Actif' => htmlspecialchars($liste_T_hote[12]),
				'Commentaire' => htmlspecialchars($liste_T_hote[9]),
				'Consigne' => htmlspecialchars($liste_T_hote[10]),
				'Detail_Consigne' => htmlspecialchars($liste_T_hote[11]),
				'Type_Action' => htmlspecialchars($liste_T_hote[13]),
				'nom_hote2' => htmlspecialchars($liste_T_hote[0]),
				'id_demande2' => $ID_Demande,
				'description2' => htmlspecialchars($liste_T_hote[2]),
				'ip_hote2' => htmlspecialchars($liste_T_hote[1]),
				'type_hote2' => htmlspecialchars($liste_T_hote[4]),
				'id_localisation2' => htmlspecialchars($liste_T_hote[3]),
				'os2' => htmlspecialchars($liste_T_hote[5]), // récupérer la valeur texte pour affichage
				'architecture2' => htmlspecialchars($liste_T_hote[6]),
				'langue2' => htmlspecialchars($liste_T_hote[7]),
				'fonction2' => htmlspecialchars($liste_T_hote[8]),
				'controle_actif2' => htmlspecialchars($liste_T_hote[12]),
				'commentaire2' => htmlspecialchars($liste_T_hote[9]),
				'consigne2' => htmlspecialchars($liste_T_hote[10]),
				'detail_consigne2' => htmlspecialchars($liste_T_hote[11]),
				'type_action2' => htmlspecialchars($liste_T_hote[13])
			)) or die(print_r($MAJ_hote->errorInfo()));
		//};
		addlog("INSERTION données: nom_hote=" . htmlspecialchars($liste_T_hote[0]) . ",id_demande=" . $ID_Demande . ",description=" . htmlspecialchars($liste_T_hote[2]) .",ip_hote=" . htmlspecialchars($liste_T_hote[1]) .",type_hote=" . htmlspecialchars($liste_T_hote[4]).",id_localisation=" . htmlspecialchars($liste_T_hote[3]).",os=" . htmlspecialchars($liste_T_hote[5]).",architecture=" . htmlspecialchars($liste_T_hote[6]).",langue=" . htmlspecialchars($liste_T_hote[7]).",fonction=" . htmlspecialchars($liste_T_hote[8]).",controle_actif=" . htmlspecialchars($liste_T_hote[12]).",commentaire=" . htmlspecialchars($liste_T_hote[9]).",consigne=" . htmlspecialchars($liste_T_hote[10]).",detail_consigne=" . htmlspecialchars($liste_T_hote[11]).",type_action=" . htmlspecialchars($liste_T_hote[13]) ."");
	};

	// epuration de la demande des hotes retirés
	$Lst_Hote_Base = $bdd_supervision->prepare('SELECT Nom_Hote,IP_Hote FROM hote WHERE Type_Action<> :type_action AND ID_Demande= :ID_Demande;');
	$Lst_Hote_Base->execute(array(
		//'type_action' => "Traite",
		'type_action' => "NC",
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Hote_Base->errorInfo()));

	while ($Res_Hote_Base = $Lst_Hote_Base->fetch())
	{
		//addlog("boucle principale");
		addlog("Hote_en_Base=". $Res_Hote_Base['Nom_Hote']);
		$Trouve = "Non";
		//addlog("trouve1=".$Trouve);
		for ($i = 0;$i<$NbHote;$i++)
		{
			//addlog("debug liste_hote=". $liste_hote[$i]);
			$Lst_Hote_Dem = explode("|",$liste_hote[$i]);

			//addlog("Compar:". $Lst_Hote_Dem[0].$Lst_Hote_Dem[1]." == ".$Res_Hote_Base['Nom_Hote'].$Res_Hote_Base['IP_Hote']);
			if ($Lst_Hote_Dem[0].$Lst_Hote_Dem[1] == $Res_Hote_Base['Nom_Hote'].$Res_Hote_Base['IP_Hote'])
			{
				$Trouve = "Oui";
				addlog("Hôte trouvé dans l'interface, on ne supprime pas.");
				break;
			};
		};
				//addlog("trouve3=".$Trouve);

		if ($Trouve == "Non")
		{
			addlog("hote non trouvé dans l'interface, purge:".$Res_Hote_Base['Nom_Hote']);
			$DEL_Hote = $bdd_supervision->prepare('DELETE FROM hote 
				WHERE Nom_Hote= :Nom_Hote AND IP_Hote = :IP_Hote AND Type_Action <> :type_action AND ID_Demande= :ID_Demande;');
			$DEL_Hote->execute(array(
				'Nom_Hote' => $Res_Hote_Base['Nom_Hote'],
				'IP_Hote' => $Res_Hote_Base['IP_Hote'],
				//'type_action' => "Traite",
				'type_action' => "NC",
				'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Hote->errorInfo()));
		};
	};
};

if ($liste_service[0] != "")  // S'il y a au moins un service
{
	// Calcul du nombre de service à mettre à jour
	$NbService = count($liste_service);
	addlog("NbService=".$NbService);
	for ($i = 0;$i<$NbService;$i++)
	{
		addlog("liste_service=".($liste_service[$i]));
		$liste_T_service = explode("|",$liste_service[$i]);
		//addlog("Nom_service=".($liste_T_service[0]));
		//addlog("Nom_Periode=".($liste_T_service[2]));
		//addlog("Frequence=".($liste_T_service[6]));
		//addlog("Controle_Actif=".($liste_T_service[3]));
		//addlog("ID_Modele_Service=".($liste_T_service[5]));
		//addlog("Consigne=".($liste_T_service[7]));
		//addlog("Detail_Consigne=".($liste_T_service[8]));
		//addlog("Type_Action=".($liste_T_service[9]));
		//addlog("Commentaire=".($liste_T_service[10]));
		//addlog("ID_Hote_Centreon=".($liste_T_service[4]));
		//addlog("Parametres=".($liste_T_service[11]));
		addlog("requete insertion Service:");
		addlog("INSERT INTO service (Nom_Service, ID_Demande, ID_Hote, Nom_Periode, Frequence, Controle_Actif, ID_Modele_Service, Parametres, Consigne, Detail_Consigne, Type_Action, Commentaire, ID_Hote_Centreon) 
			VALUES ('" . htmlspecialchars($liste_T_service[0]) . "'," . $ID_Demande . "," . htmlspecialchars($liste_T_service[1]) . ",'" . htmlspecialchars($liste_T_service[2]) . "','" . htmlspecialchars($liste_T_service[6]) . "','" . htmlspecialchars($liste_T_service[3]) . "'," . htmlspecialchars($liste_T_service[5]) . ",'" . htmlspecialchars($liste_T_service[11]) . "','" . htmlspecialchars($liste_T_service[7]) . "','" . htmlspecialchars($liste_T_service[8]) . "','" . htmlspecialchars($liste_T_service[9]) . "','" . htmlspecialchars($liste_T_service[10]) . "'," . htmlspecialchars($liste_T_service[4]) . ")
			ON DUPLICATE KEY UPDATE Nom_Service= '".htmlspecialchars($liste_T_service[0])."', ID_Demande= ".$ID_Demande.", ID_Hote= ".htmlspecialchars($liste_T_service[1]).", Nom_Periode= '".htmlspecialchars($liste_T_service[2])."', Frequence= '".htmlspecialchars($liste_T_service[6])."', Controle_Actif= '".htmlspecialchars($liste_T_service[3])."', ID_Modele_Service= ".htmlspecialchars($liste_T_service[5]).", Parametres= '".htmlspecialchars($liste_T_service[11])."', Consigne= '".htmlspecialchars($liste_T_service[7])."', Detail_Consigne= '".htmlspecialchars($liste_T_service[8])."', Type_Action= '".htmlspecialchars($liste_T_service[9])."', Commentaire= '".htmlspecialchars($liste_T_service[10])."', ID_Hote_Centreon= ".htmlspecialchars($liste_T_service[4]).";");
		addlog("##### DEBUG SI NECESSAIRE pour réinsertion manuelle ##### UPDATE service SET ID_Hote= ".htmlspecialchars($liste_T_service[1]).", Nom_Periode= '".htmlspecialchars($liste_T_service[2])."', Frequence= '".htmlspecialchars($liste_T_service[6])."', Controle_Actif= '".htmlspecialchars($liste_T_service[3])."', ID_Modele_Service= ".htmlspecialchars($liste_T_service[5]).", Parametres= '".htmlspecialchars($liste_T_service[11])."', Consigne= '".htmlspecialchars($liste_T_service[7])."', Detail_Consigne= '".htmlspecialchars($liste_T_service[8])."', Type_Action= '".htmlspecialchars($liste_T_service[9])."', Commentaire= '".htmlspecialchars($liste_T_service[10])."', ID_Hote_Centreon= ".htmlspecialchars($liste_T_service[4])." WHERE Nom_Service= '".htmlspecialchars($liste_T_service[0])."' AND ID_Hote= ".htmlspecialchars($liste_T_service[1])." AND ID_Demande= ".$ID_Demande.";");
		
		$MAJ_service = $bdd_supervision->prepare('INSERT INTO service 
			(Nom_Service, ID_Demande, ID_Hote, Nom_Periode, Frequence, Controle_Actif, ID_Modele_Service, Parametres, Consigne, Detail_Consigne, Type_Action, Commentaire, ID_Hote_Centreon)
			VALUES (:Nom_Service, :ID_Demande, :ID_Hote, :Nom_Periode, :Frequence, :Controle_Actif, :ID_Modele_Service, :Parametres, :Consigne, :Detail_Consigne, :Type_Action, :Commentaire, :id_hote_centreon)
			ON DUPLICATE KEY UPDATE Nom_Service= :nom_service2, ID_Demande= :id_demande2, ID_Hote= :id_hote2, Nom_Periode= :nom_periode2, Frequence= :frequence2, Controle_Actif= :controle_actif2, ID_Modele_Service= :id_modele_service2, Parametres= :parametres2, Consigne= :consigne2, Detail_Consigne= :detail_consigne2, Type_Action= :type_action2, Commentaire= :commentaire2, ID_Hote_Centreon= :id_hote_centreon2');
		$MAJ_service->execute(array(
			'Nom_Service' => htmlspecialchars($liste_T_service[0]),
			'ID_Demande' => $ID_Demande,
			//'Nom_Hote' => htmlspecialchars($liste_T_service[1]),
			'ID_Hote' => htmlspecialchars($liste_T_service[1]),
			'Nom_Periode' => htmlspecialchars($liste_T_service[2]),
			//'Frequence' => htmlspecialchars($liste_T_service[3]),
			'Frequence' => htmlspecialchars($liste_T_service[6]),
			//'Controle_Actif' => htmlspecialchars($liste_T_service[4]),
			'Controle_Actif' => htmlspecialchars($liste_T_service[3]),
			//'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
			//'ID_Modele_Service' => htmlspecialchars($liste_T_service[4]),
			'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
			'Parametres' => htmlspecialchars($liste_T_service[11]),
			'Consigne' => htmlspecialchars($liste_T_service[7]),
			'Detail_Consigne' => htmlspecialchars($liste_T_service[8]),
			'Type_Action' => htmlspecialchars($liste_T_service[9]),
			'Commentaire' => htmlspecialchars($liste_T_service[10]),
			'id_hote_centreon' => htmlspecialchars($liste_T_service[4]),
			'nom_service2' => htmlspecialchars($liste_T_service[0]),
			'id_demande2' => $ID_Demande,
			//'Nom_Hote' => htmlspecialchars($liste_T_service[1]),
			'id_hote2' => htmlspecialchars($liste_T_service[1]),
			'nom_periode2' => htmlspecialchars($liste_T_service[2]),
			//'Frequence' => htmlspecialchars($liste_T_service[3]),
			'frequence2' => htmlspecialchars($liste_T_service[6]),
			//'Controle_Actif' => htmlspecialchars($liste_T_service[4]),
			'controle_actif2' => htmlspecialchars($liste_T_service[3]),
			//'ID_Modele_Service' => htmlspecialchars($liste_T_service[5]),
			'id_modele_service2' => htmlspecialchars($liste_T_service[5]),
			'parametres2' => htmlspecialchars($liste_T_service[11]),
			'consigne2' => htmlspecialchars($liste_T_service[7]),
			'detail_consigne2' => htmlspecialchars($liste_T_service[8]),
			'type_action2' => htmlspecialchars($liste_T_service[9]),
			'commentaire2' => htmlspecialchars($liste_T_service[10]),
			'id_hote_centreon2' => htmlspecialchars($liste_T_service[4])
		)) or die(print_r($MAJ_service->errorInfo()));
	};
	addlog(print_r($MAJ_service));
};

// Mise à jour Nom_Hote suite à insertion
//	$UPD_Service = $bdd_supervision->prepare('UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= :ID_Demande');
	$UPD_Service = $bdd_supervision->prepare('UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote, S.ID_Hote_Centreon=H.ID_Hote_Centreon WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= :ID_Demande');
	$UPD_Service->execute(array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Service->errorInfo()));
	addlog("Mise a jour Nom hote");
	addlog("UPDATE service AS S, hote AS H SET S.Nom_Hote=H.Nom_Hote WHERE S.ID_Hote=H.ID_Hote AND S.ID_Demande= " . $ID_Demande . ";");

// Mise à jour Type_action=Creer suite à duplication ou ajout
	$UPD_Service = $bdd_supervision->prepare('UPDATE service SET Type_Action="Creer" WHERE selection IS NULL AND ID_Demande= :ID_Demande');
	$UPD_Service->execute(array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Service->errorInfo()));
	addlog("Mise a jour Type_Action=Creer pour selection=NULL");
	addlog("UPDATE service SET Type_Action='Creer' WHERE selection IS NULL AND ID_Demande= " . $ID_Demande . ";");
	
/*
 * Désactivé le 23/12/14 puisque géré dans la suppr du fieldset => génère des suppressions à tord! => cas d'un ID_Hote non récupéré
// epuration de la demande des services retirés et des doublons
// vérifier si toujours utile puisque suppression gérée lors de la suppression du fieldset
//	$Lst_Service_Base = $bdd_supervision->prepare('SELECT Nom_Service, Nom_Hote FROM service WHERE ID_Demande= :ID_Demande;');
      $Lst_Service_Base = $bdd_supervision->prepare('SELECT Nom_Service, ID_Hote FROM service WHERE ID_Demande= :ID_Demande;');
	$Lst_Service_Base->execute(array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Service_Base->errorInfo()));

	while ($Res_Service_Base = $Lst_Service_Base->fetch())
	{
		addlog("boucle principale");
		addlog("Service_Base=". $Res_Service_Base['Nom_Service']);
		$Trouve = False;
		for ($i = 0;$i<$NbService;$i++)
		{
			addlog("debug liste_service".$i."=". $liste_service[$i]);
			$Lst_Service_Dem = explode("|",$liste_service[$i]);

			addlog("Service_Dem=". $Lst_Service_Dem[0].$Lst_Service_Dem[1]);
//                        if ($Lst_Service_Dem[0].$Lst_Service_Dem[1] == $Res_Service_Base['Nom_Service'].$Res_Service_Base['Nom_Hote'])
			if ($Lst_Service_Dem[0].$Lst_Service_Dem[1] == $Res_Service_Base['Nom_Service'].$Res_Service_Base['ID_Hote'])
			{
				$Trouve = True;
				addlog("service trouvé, on ne purge pas.");
				break;
			};
		};
		if ($Trouve == False)
		{
			addlog("service non trouvé, purge=".$Res_Service_Base['Nom_Service']);
			$DEL_Service = $bdd_supervision->prepare('DELETE FROM service 
				WHERE Nom_Service= :Nom_Service AND ID_Hote = :ID_Hote AND ID_Demande= :ID_Demande;');
			$DEL_Service->execute(array(
				'Nom_Service' => $Res_Service_Base['Nom_Service'],
				'ID_Hote' => $Res_Service_Base['ID_Hote'],
				'ID_Demande' => $ID_Demande
			)) or die(print_r($DEL_Service->errorInfo()));
		};
	};
	// déprécié le 28/11/14
	//include('gestion_doublon_service.php'); // A vérifier si toujours utile
//};

 */

if ($liste_plage[0] != "")  // S'il y a au moins une plage
{
	// Calcul du nombre de plage à mettre à jour
	$NbPlage = count($liste_plage);
	addlog("NbPlage=".$NbPlage);

	for ($i = 0;$i<$NbPlage;$i++)
	{
		addlog("liste_plage=".($liste_plage[$i]));
		$liste_T_plage = explode("|",$liste_plage[$i]);
		addlog("Nom_plage=".($liste_T_plage[0]));
	//	0				1				2				3				4				5				6		7			8				9
	// 	09h-18h - L-V	#09:00-18:00	#09:00-18:00	#09:00-18:00	#09:00-18:00	#09:00-18:00	#samedi	#dimanche	#commentaire 	#OK

//		// epuration plage avant réinsertion
//	$DEL_plage = $bdd_supervision->prepare('DELETE FROM periode_temporelle WHERE ID_Demande = :ID_Demande;');
//	$DEL_plage->execute(array(
//		'ID_Demande' => $ID_Demande
//	)) or die(print_r($DEL_plage->errorInfo()));


		$Select_plage = $bdd_supervision->prepare('SELECT ID_Periode_Temporelle, Nom_Periode FROM periode_temporelle WHERE Nom_Periode = :Nom_Periode AND ID_Demande = :ID_Demande;');
		$Select_plage->execute(array(
			'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
			'ID_Demande' => $ID_Demande
		)) or die(print_r($Select_plage->errorInfo()));
		
			$UPDATE_plage = False;
			$res_Select_Plage = $Select_plage->fetchAll();
			foreach($res_Select_Plage as $Plage)
			{
				if ($Plage[1] == $liste_T_plage[0]) // Si la plage correspond
				addlog("Plage=".$Plage[1]);
				$UPDATE_plage = True;
			};

		if ($UPDATE_plage==True)
		{
			addlog("MAJ Table Plage");
			$MAJ_plage = $bdd_supervision->prepare('UPDATE periode_temporelle 
				SET Lundi= :Lundi,
					Mardi= :Mardi,
					Mercredi= :Mercredi,
					Jeudi= :Jeudi,
					Vendredi= :Vendredi,
					Samedi= :Samedi,
					Dimanche= :Dimanche,
					Commentaire= :Commentaire,
					Type_Action= :Type_Action
				WHERE Nom_Periode= :Nom_Periode AND ID_Demande = :ID_Demande;');
			$MAJ_plage->execute(array(
				'Lundi' => htmlspecialchars($liste_T_plage[1]),
				'Mardi' => htmlspecialchars($liste_T_plage[2]),
				'Mercredi' => htmlspecialchars($liste_T_plage[3]),
				'Jeudi' => htmlspecialchars($liste_T_plage[4]),
				'Vendredi' => htmlspecialchars($liste_T_plage[5]),
				'Samedi' => htmlspecialchars($liste_T_plage[6]),
				'Dimanche' => htmlspecialchars($liste_T_plage[7]),
				'Commentaire' => htmlspecialchars($liste_T_plage[8]),
				'Type_Action' => htmlspecialchars($liste_T_plage[9]),
				'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
				'ID_Demande' => $ID_Demande
				)) or die(print_r($MAJ_plage->errorInfo()));
		} else
		{
			addlog("INSERT Table Plage");
			$MAJ_plage = $bdd_supervision->prepare('INSERT INTO periode_temporelle 
				(ID_Demande, Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche, Commentaire, Type_Action)
				VALUES (:ID_Demande, :Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche, :Commentaire, :Type_Action)');
			$MAJ_plage->execute(array(
				'ID_Demande' => $ID_Demande,
				'Nom_Periode' => htmlspecialchars($liste_T_plage[0]),
				'Lundi' => htmlspecialchars($liste_T_plage[1]),
				'Mardi' => htmlspecialchars($liste_T_plage[2]),
				'Mercredi' => htmlspecialchars($liste_T_plage[3]),
				'Jeudi' => htmlspecialchars($liste_T_plage[4]),
				'Vendredi' => htmlspecialchars($liste_T_plage[5]),
				'Samedi' => htmlspecialchars($liste_T_plage[6]),
				'Dimanche' => htmlspecialchars($liste_T_plage[7]),
				'Commentaire' => htmlspecialchars($liste_T_plage[8]),
				'Type_Action' => htmlspecialchars($liste_T_plage[9])
			)) or die(print_r($MAJ_plage->errorInfo()));
		};
	};

// epuration de la demande des plages retirés (passage au Type_Action "OK")
// vérifier si toujours utile puisque suppression gérée lors de la suppression du fieldset
	$Lst_Plage_Base = $bdd_supervision->prepare('SELECT Nom_Periode FROM periode_temporelle WHERE ID_Demande= :ID_Demande;');
	$Lst_Plage_Base->execute(array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($Lst_Plage_Base->errorInfo()));

	while ($Res_Plage_Base = $Lst_Plage_Base->fetch())
	{
		addlog("boucle principale");
		addlog("Plage_Base=". $Res_Plage_Base['Nom_Periode']);
		$Trouve = False;
		for ($i = 0;$i<$NbPlage;$i++)
		{
			addlog("debug liste_plage=". $liste_plage[$i]);
			$Lst_Plage_Dem = explode("|",$liste_plage[$i]);

			addlog("Plage_Dem=". $Lst_Plage_Dem[0]);
			if ($Lst_Plage_Dem[0] == $Res_Plage_Base['Nom_Periode'])
			{
				$Trouve = True;
				addlog("trouve");
				break;
			};
		};
		if ($Trouve == False)
		{
			addlog("purge periode=".$Res_Plage_Base['Nom_Periode']); // on purge pas vraiment pour conserver la dispo de la plage sur les autres services
			$UPD_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle 
				SET Type_Action= :Type_Action
				WHERE Nom_Periode= :Nom_Periode AND ID_Demande= :ID_Demande;');
			$UPD_Plage->execute(array(
				'Type_Action' => "NC",
				'Nom_Periode' => $Res_Plage_Base['Nom_Periode'],
				'ID_Demande' => $ID_Demande
			)) or die(print_r($UPD_Plage->errorInfo()));
		};
	};
};


// /**
//  * Supprimer les accents
//  *
//  * @param string $str chaîne de caractères avec caractères accentués
//  * @param string $encoding encodage du texte (exemple : utf-8, ISO-8859-1 ...)
//  */
// function suppr_accents($str, $encoding='utf-8')
// {
// 	// transformer les caractères accentués en entités HTML
// 	$str = htmlentities($str, ENT_NOQUOTES, $encoding);

// 	// remplacer les entités HTML pour avoir juste le premier caractères non accentués
// 	// Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
// 	$str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

// 	// Remplacer les ligatures tel que : Œ, Æ ...
// 	// Exemple "Å“" => "oe"
// 	$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
// 	// Supprimer tout le reste
// 	$str = preg_replace('#&[^;]+;#', '', $str);

// 	return $str;
// }