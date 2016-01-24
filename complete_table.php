<?php
// session_start(); // page appelée depuis une autre donc inutile
if (session_id()=='')
{
session_start();
};
addlog("Chargement complete_table.php");
include_once('connexion_sql_centreon.php');
include_once('connexion_sql_supervision.php');

// récupération de la ref demande
$ID_Demande= htmlspecialchars($_SESSION['ID_dem']);
addlog("Traitement de la demande [" . $ID_Demande . "].");

// on doit maintenant boucher les trous
	// récupérer toutes les infos complémentaires des hôtes sélectionnés pour cette demande

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//liste des hôtes de la demande
addlog("COMPLETE_TABLE: Traitement des hotes...");
//$liste_hote_demande = $bdd_supervision->prepare('SELECT ID_Hote_Centreon, Nom_Hote FROM hote WHERE ID_Demande = :ID_Demande');
//$liste_hote_demande = $bdd_supervision->prepare('SELECT ID_Hote_Centreon FROM hote WHERE ID_Demande = :ID_Demande');
//$liste_hote_demande = $bdd_supervision->prepare('SELECT ID_Hote_Centreon FROM hote_temp WHERE ID_Demande = :ID_Demande');
try {
	$bdd_supervision->beginTransaction();
	$liste_hote_demande = $bdd_supervision->prepare('SELECT ID_Hote_Centreon FROM hote WHERE ID_Demande = :ID_Demande');
	$liste_hote_demande->execute(Array(
			'ID_Demande' => $ID_Demande)) or die(print_r($liste_hote_demande->errorInfo()));
	
	// on boucle sur la liste des hôte pour MAJ table
//$i = 1;
while ($res_liste_hote_demande = $liste_hote_demande->fetch())
{
//		addlog("passe n°". $i);
	$ID_Hote_Centreon = htmlspecialchars($res_liste_hote_demande['ID_Hote_Centreon']);
		addlog("Selection categories");
//	$select_categ_hote = $bdd_centreon->prepare('SELECT Distinct(host_name), hc_name, HC_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name');
	$select_categ_hote = $bdd_centreon->prepare('SELECT host_name, hc_name, HC_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name');

	$select_categ_hote->execute(Array(
	'ID_Hote' => $ID_Hote_Centreon)) or die(print_r($select_categ_hote->errorInfo()));
//		$select_categ_hote = $bdd_centreon->query('SELECT Distinct(host_name), hc_name, HC_alias FROM ((((vInventaireServices AS vIS LEFT JOIN host AS H ON vIS.host_id=H.host_id) LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id)) WHERE H.host_id= ' . $ID_Hote_Centreon . ' ORDER BY H.host_name');
	// extraction 
		addlog("extraction Localisation et Type");
		$Localisation="";
		$Type="";
		$Cat_Archi="";
		$Cat_OS="";
		$Cat_Langue="";
		$Cat_Type="";
		$Cat_Fonction="";
	
	while($res_select_categ_hote = $select_categ_hote->fetch())
	{
		
		if ($Localisation == "")
		{
			$Localisation = stristr(htmlspecialchars($res_select_categ_hote[0]),'-',TRUE); // récupère la localisation => les caractères avant le premier tiret
			addlog($Localisation);
		};
		if ($Type == "")
		{
			$Type = stristr(substr(stristr(htmlspecialchars($res_select_categ_hote[0]),'-'),1),'-',TRUE); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
			addlog($Type);
		};
		$categorie = stristr(htmlspecialchars($res_select_categ_hote[1]),'_',TRUE); // récupère la chaine AVANT le _
		if ($categorie == "Architecture") // si categorie est "Archi"
		{
			$Cat_Archi = substr(stristr(htmlspecialchars($res_select_categ_hote[1]),'_',FALSE),1); // récupère la chaine APRES le _
			addlog($Cat_Archi);
		}else if ($categorie == "OS") // si categorie est "OS"
		{
			$Cat_OS = substr(stristr(htmlspecialchars($res_select_categ_hote[1]),'_',FALSE),1); // récupère la chaine APRES le _
			addlog($Cat_OS);
		} else if ($categorie == "Langue") // si categorie est "Langue"
		{
			$Cat_Langue = substr(stristr(htmlspecialchars($res_select_categ_hote[1]),'_',FALSE),1); // récupère la chaine APRES le _
			addlog($Cat_Langue);
		} else if ($categorie == "Type") // si categorie est "Type"
		{
			$Cat_Type = substr(stristr(htmlspecialchars($res_select_categ_hote[1]),'_',FALSE),1); // récupère la chaine APRES le _
			addlog($Cat_Type);
		} else if ($categorie == "Fonction") // si categorie est "Fonction"
		{
			$Cat_Fonction = substr(stristr(htmlspecialchars($res_select_categ_hote[1]),'_',FALSE),1); // récupère la chaine APRES le _
			addlog($Cat_Fonction);
		} else if ($categorie <> "") // si categorie n'est pas vide
		{
			$Cat_Inconnue = stristr(htmlspecialchars($res_select_categ_hote[1]),'_',TRUE); // récupère la chaine AVANT le _
			echo "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge";
			addlog("Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge");
			Return False;
//			} else
//			{
//				if (!isset($Cat_Archi)){$Cat_Archi =" ";};
//				if (!isset($Cat_OS)){$Cat_OS =" ";}
//				if (!isset($Cat_Langue)){$Cat_Langue =" ";}
//				if (!isset($Cat_Type)){$Cat_Type =" ";}
//				if (!isset($Cat_Fonction)){$Cat_Fonction =" ";}
		};
	};
// modif le 03-11-2014
//	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Type_Hote= :Type_Hote, ID_Localisation= :ID_Localisation, Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue, Type_Action= :Type_Action WHERE ID_Hote_Centreon = :ID_Hote_Centreon AND ID_Demande= :ID_Demande');
	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Type_Hote= :Type_Hote, ID_Localisation= :ID_Localisation, Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon AND ID_Demande= :ID_Demande');
	$MAJ_Hote->execute(Array(
		'Type_Hote' => $Type,
		'ID_Localisation' => $Localisation,
		'Cat_Fonction' => $Cat_Fonction,
		'Cat_Archi' => $Cat_Archi,
		'Cat_OS' => $Cat_OS,
		'Cat_Langue' => $Cat_Langue,
//		'Type_Action' => "Modifier", // on force l'action à Modifier
		//			'Cat_Type' => $Cat_Type, // deja recupéré dans le nom de l'hote
		'ID_Hote_Centreon' => $ID_Hote_Centreon,
		'ID_Demande' => $ID_Demande
	)) or die(print_r($MAJ_Hote->errorInfo()));
	addlog("champs table hote mis à jour!");
	echo "champs table hote mis à jour.";

	
	
/* déprécié le 03-11-2014
	// MAJ hote_temp avec ID_Localisation
	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote_temp SET ID_Localisation= :ID_Localisation WHERE ID_Hote_Centreon = :ID_Hote_Centreon AND ID_Demande= :ID_Demande');
	$MAJ_Hote->execute(Array(
		'ID_Localisation' => $Localisation,
		'ID_Hote_Centreon' => $ID_Hote_Centreon,
		'ID_Demande' => $ID_Demande
	)) or die(print_r($MAJ_Hote->errorInfo()));
	addlog("champs table hote_temp mis à jour!");
	echo "champs table hote_temp mis à jour.";
*/
	//$i++;
};

// mise à jour du type d'action à effecuer pour filtrer l'affichage sur l'onglet paramétrage
// => si selection=true => si controle_actif=actif => Modifier sinon Activer
// => si selection=false => NC
$MAJ_Hote2 = $bdd_supervision->prepare('UPDATE hote SET type_action=if(selection="true",if(Controle_Actif="actif","Modifier","Activer"),"NC") WHERE ID_Demande= :id_demande');
$MAJ_Hote2->execute(Array(
		'id_demande' => $ID_Demande
)) or die(print_r($MAJ_Hote2->errorInfo()));
//	$MAJ_Hote3 = $bdd_supervision->query('UPDATE hote SET type_action="Traite" WHERE selection="false"') or die(print_r($MAJ_Hote3->errorInfo()));
//	$MAJ_Hote3 = $bdd_supervision->query('UPDATE hote SET type_action="NC" WHERE selection="false"') or die(print_r($MAJ_Hote3->errorInfo()));



//////////////////////////////////////////////////////////////////////////////////////////////////////////
//liste des services de la demande
addlog("COMPLETE_TABLE: Traitement des services...");
//$liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Periode, Nom_Hote FROM service WHERE ID_Demande = :ID_Demande');
//$liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Hote FROM service WHERE ID_Demande = :ID_Demande');
$liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Hote FROM service WHERE ID_Demande = :ID_Demande AND ID_Service_Centreon <>0'); // dans certains cas non reproduis pour l'instant, vérole les paramètres du service car ne trouve pas les infos dans la base centreon pour id=0
$liste_service_demande->execute(Array(
	'ID_Demande' => $ID_Demande
)) or die(print_r($liste_service_demande->errorInfo()));
	// on boucle sur la liste des services pour MAJ table
// $num_service=1;
while ($res_liste_service_demande = $liste_service_demande->fetch())
{
// 	echo "Numero_Service=". $num_service;
	$ID_Service_Centreon = htmlspecialchars($res_liste_service_demande['ID_Service_Centreon']);
	addlog("ID_Service_Centreon=".$ID_Service_Centreon);
	$ID_Hote_Centreon = htmlspecialchars($res_liste_service_demande['ID_Hote_Centreon']);
	addlog("ID_Hote_Centreon=".$ID_Hote_Centreon);
	//	$Nom_Periode = htmlspecialchars($res_liste_service_demande['Nom_Periode']);
//	$Nom_Periode = $res_liste_service_demande['Nom_Periode'];
	$Nom_Hote = htmlspecialchars($res_liste_service_demande['Nom_Hote']);
	addlog("Nom_Hote=".$Nom_Hote);
	
	addlog("récupération des infos services");
// 	if ($ID_Hote_Centreon != NULL) // correction le 18/03/15 l'ID_Hote_Centreon n'est jamais NULL mais à 0 si création de service
 	if ($ID_Hote_Centreon != 0)
	{
		// mise à jour de l'ID_Hote à partir de l'ID_Hote_Centreon s'il existe
		$upd_service = $bdd_supervision->prepare('
				UPDATE service AS S,hote AS H
				 SET S.ID_Hote=(SELECT DISTINCT(ID_Hote) FROM hote WHERE ID_Demande = :id_demande1 AND ID_Hote_Centreon= :id_hote_centreon1),
					 S.Nom_Hote=(SELECT DISTINCT(Nom_Hote) FROM hote WHERE ID_Demande = :id_demande2 AND ID_Hote_Centreon= :id_hote_centreon2)
				 WHERE S.ID_Demande = :id_demande3 AND S.ID_Hote_Centreon = H.ID_Hote_Centreon AND ID_Service_Centreon= :id_service_centreon');
		$upd_service->execute(Array(
			'id_demande1' => $ID_Demande,
				'id_hote_centreon1' => $ID_Hote_Centreon,
				'id_demande2' => $ID_Demande,
				'id_hote_centreon2' => $ID_Hote_Centreon,
				'id_demande3' => $ID_Demande,
				'id_service_centreon' => $ID_Service_Centreon
		)) or die(print_r($upd_service->errorInfo()));
	addlog("UPD_service: UPDATE service AS S,hote AS H
				 SET S.ID_Hote=(SELECT DISTINCT(ID_Hote) FROM hote WHERE ID_Demande = " . $ID_Demande . " AND ID_Hote_Centreon= " . $ID_Hote_Centreon . "),
					 S.Nom_Hote=(SELECT DISTINCT(Nom_Hote) FROM hote WHERE ID_Demande = " . $ID_Demande . " AND ID_Hote_Centreon= " . $ID_Hote_Centreon . ")
				 WHERE S.ID_Demande = " . $ID_Demande . " AND S.ID_Hote_Centreon = H.ID_Hote_Centreon AND ID_Service_Centreon= " . $ID_Service_Centreon . "");
/*
 * Déprécié le 02/12/14 => ne doit pas arriver puisque le nom est present dans la liste
		// mise à jour du Nom_Hote si NULL ce qui ne devrait pas arriver sur un hôte importé de centreon
		if ($Nom_Hote == NULL)
		{
			$req_Nom_Hote = $bdd_centreon->prepare('SELECT host_name FROM host WHERE host_id = :ID_Hote_Centreon');
			$req_Nom_Hote->execute(Array(
				'ID_Hote_Centreon' => $ID_Hote_Centreon
			)) or die(print_r($req_Nom_Hote->errorInfo()));
			while ($res_Nom_Hote = $req_Nom_Hote->fetch())
			{
				$Nom_Hote = substr(stristr(substr(stristr($res_Nom_Hote['host_name'],'-'),1),'-'),1); //enlève localisation et type
				$upd_service = $bdd_supervision->prepare('UPDATE service SET Nom_Hote= :Nom_Hote WHERE ID_Demande = :ID_Demande AND ID_Hote_Centreon = :ID_Hote_Centreon');
				$upd_service->execute(Array(
					'Nom_Hote' => $Nom_Hote,
					'ID_Demande' => $ID_Demande,
					'ID_Hote_Centreon' => $ID_Hote_Centreon
				)) or die(print_r($upd_service->errorInfo()));
			};
		};
*/
	};
	// met à jour le modèle de service Centreon
	$req_C_service = $bdd_centreon->prepare('SELECT service_template_model_stm_id AS ID_Modele_Service FROM service WHERE service_id = :ID_Service_Centreon');
	$req_C_service->execute(Array(
		'ID_Service_Centreon' => $ID_Service_Centreon
	)) or die(print_r($req_C_service->errorInfo()));
	while ($res_req_C_service = $req_C_service->fetch())
	{
		$upd_service = $bdd_supervision->prepare('UPDATE service SET ID_Modele_Service_Centreon= :ID_Modele_Service WHERE ID_Demande = :ID_Demande AND ID_Service_Centreon = :ID_Service_Centreon');
		$upd_service->execute(Array(
			'ID_Modele_Service' => $res_req_C_service['ID_Modele_Service'],
			'ID_Demande' => $ID_Demande,
			'ID_Service_Centreon' => $ID_Service_Centreon
		)) or die(print_r($upd_service->errorInfo()));
	};
	
	// met à jour le modèle de service Changement
	$upd_service = $bdd_supervision->prepare('UPDATE service AS S INNER JOIN relation_modeles AS RM ON S.ID_Modele_Service_Centreon=RM.ID_Modele_Service_Centreon SET S.ID_Modele_Service=RM.ID_Modele_Service WHERE ID_Demande = :ID_Demande');
	$upd_service->execute(Array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($upd_service->errorInfo()));

	// vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
	$req_type_modele = $bdd_supervision->prepare('SELECT MS.MS_EST_MACRO FROM service AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Service_Centreon = :ID_Service_Centreon AND ID_Demande = :ID_Demande');
	$req_type_modele->execute(Array(
		'ID_Service_Centreon' => $ID_Service_Centreon,
		'ID_Demande' => $ID_Demande
	)) or die(print_r($req_type_modele->errorinfo()));
	
	while ($res_type_modele = $req_type_modele->fetch()) // ne doit retourner qu'un seul enregistrement
	{
		if ($res_type_modele[0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
		{
// appel procédure commune de traitement des macros
			include('requete_traitement_macros.php');

// 			$EST_MACRO = True;
// 			addlog("EST_MACRO=".$EST_MACRO);
				
// 			// récupère les arguments de type Macro
// 			//1) récupère la liste exhaustive des macro liées à la commande avec un maximum de 7 modèles (ce qui doit être largement suffisant)
// 			//	+-----------------------------------------------------------------------------------------------------------+
// 			//	| Macro                                                                                                     |
// 			//	+-----------------------------------------------------------------------------------------------------------+
// 			//	| $_SERVICEINTERFACEID$ -w $_SERVICEWARNING$ -c $_SERVICECRITICAL$ -T $_SERVICEIFSPEED$ -S $_SERVICE64BITS$ |
// 			//	+-----------------------------------------------------------------------------------------------------------+
// // 			$req_Select_Macro = $bdd_centreon->prepare('
// // 			SELECT REPLACE(TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))),"\","") AS Macro
// // 			FROM service AS S
// // 			LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// // 			LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// // 			LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// // 			LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// // 			LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// // 			LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// // 			LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// // 			LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
// // 			WHERE c.command_line IS NOT NULL
// // 				AND TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))) <> ""
// // 				AND S.service_id= :ID_Service_Centreon');
// /**
//  * 			$req_Select_Macro = $bdd_centreon->prepare('
// 			SELECT TRIM(SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))) AS Macro
// 			 FROM service AS S 
// 			 LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// 			 LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// 			 LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// 			 LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// 			 LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// 			 LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// 			 LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// 			 LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
// 			 WHERE c.command_line IS NOT NULL AND S.service_id= :ID_Service_Centreon');
//  */
// 			$req_Select_Macro = $bdd_centreon->prepare('
// 			SELECT SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)-2) AS Macro
// 			 FROM service AS S
// 			 LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// 			 LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// 			 LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// 			 LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// 			 LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// 			 LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// 			 LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// 			 LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
// 			 WHERE c.command_line IS NOT NULL AND S.service_id= :ID_Service_Centreon');
// 			$req_Select_Macro->execute(Array(
// 				'ID_Service_Centreon' => $ID_Service_Centreon
// 			)) or die(print_r($req_Select_Macro->errorinfo()));

//  			echo '<pre>';
//  			print_r($req_Select_Macro);
//  			echo '</pre>';

// 			//2) extrait chaque Macro de la chaine
// 			$Chaine_Macro = "";
// 			while ($res_Select_Macro = $req_Select_Macro->fetch())
// 			{
// 				$Chaine_Macro .= " " . htmlspecialchars($res_Select_Macro['Macro']);
// 			};
// 			addlog("Chaine_Macro=".TRIM($Chaine_Macro));
// 			$T_Chaine_Macro = explode(" ",TRIM($Chaine_Macro)); // découpe la chaine en tableau par les espaces
// //			$T_Chaine_Macro = explode("$",substr(TRIM($Chaine_Macro),1,strlen(TRIM($Chaine_Macro))-1)); // découpe la chaine en tableau par le dollar
			
// // 			echo '<pre>';
// // 			print_r($T_Chaine_Macro);
// // 			echo '</pre>';

// 			$NbLigne=count($T_Chaine_Macro);
// 			//echo "NbLigne=".$NbLigne;
// 			$Liste_Macro = Array(); // recrée un nouveau tableau qui contiendra uniquement les noms des macro
// 			$i=0;
// 			for ($j=0;$j<$NbLigne;$j++)
// 			{
// 				/**
// 				 * redécoupe les chaines pour extraire les valeurs des MACRO
// 				 * cas possibles
// 				 * 	--warning-in-traffic=$_SERVICEWARNING$
// 				 * 	$_SERVICEINTERFACEID$'
// 				 * 	--critical-in-traffic='$_SERVICECRITICAL$'
// 				 * 	--interface='^$_SERVICEINTERFACE$$$'
// 				 */

// 				/**
// 				 * récupérer la position du premier $
// 				 */
// 				$ChaineBrute=$T_Chaine_Macro[$j];
// //				echo "chaineMacrobrute_avant=" . $ChaineBrute . "\n";
// 				addlog("chaineMacrobrute_avant=" . $ChaineBrute);
// 				$ChaineBrute=preg_replace('/\${2,}/', '\$', $ChaineBrute); // Supprime les dollars multiples
// //				echo "chaineMacrobrute_apres=" . $ChaineBrute . "\n";
// 				addlog("chaineMacrobrute_apres=" . $ChaineBrute);
				
// 				$pos_premier_dollar=strpos($ChaineBrute,'$');
// 				$pos_second_dollar=strpos($ChaineBrute,'$',$pos_premier_dollar+1);
// //				echo "a partir second dollar=" . substr($ChaineBrute,$pos_second_dollar);
// 				$ChaineMacro=substr($ChaineBrute,$pos_premier_dollar,$pos_second_dollar-$pos_premier_dollar);
// //				echo "chaineMacro=" . $ChaineMacro . "\n";
// //				echo "position premier $=" . $pos_premier_dollar . "\n";
// //				echo "position second $=" . $pos_second_dollar . "\n";
// 				addlog("position premier dollar=".$pos_premier_dollar);
// 				addlog("position second dollar=".$pos_second_dollar);
// 				addlog("chaineMacro=".$ChaineMacro);
// //				addlog("test ChaineMacro=".substr($T_Chaine_Macro[$j],0,9));
// //				addlog("test ChaineMacro=".substr($T_Chaine_Macro[$j],0,8)); // _SERVICE
				
// //				if (substr($T_Chaine_Macro[$j],0,9) == "\$_SERVICE")
// 				if (substr($ChaineMacro,0,9) == "\$_SERVICE")
// //				if (substr($T_Chaine_Macro[$j],0,8) == "_SERVICE")
// 				{
// 					//$Liste_Macro[$i] = substr($res_liste_Macro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
// /**					if (strpos($ChaineMacro,'\$\$') > 0)
// 					{
// 						addlog("double dollar");
// 						$Liste_Macro[$i] = substr($ChaineMacro,9,-2); // retourne la valeur de la macro sans "$_SERVICE" et les deux derniers "$" et la stocke dans un nouveau tableau
// 					} else
// 					{
// 						addlog("simple dollar");
// 						//$Liste_Macro[$i] = substr($ChaineMacro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
// 					}
// */
// 					//$Liste_Macro[$i] = substr($ChaineMacro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
// 					$Liste_Macro[$i] = substr($ChaineMacro,9,$pos_second_dollar); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
								
// 					//$Liste_Macro[$i] = $T_Chaine_Macro[$j]; // retourne la valeur de la macro et la stocke dans un nouveau tableau
// 					addlog("valeur_macro ajoutée=".$Liste_Macro[$i]);
// 					$i++;
// 				};
// 			};

// // 			echo '<pre>';
// // 			print_r($Liste_Macro);
// // 			echo '</pre>';

// 			//3) récupérer la chaine des modèles afin de récupérer la liste des valeurs de chaque modèle
// 			//	+------------+------------+------------+------------+------------+------------+------------+------------+
// 			//	| service_id | service_id | service_id | service_id | service_id | service_id | service_id | service_id |
// 			//	+------------+------------+------------+------------+------------+------------+------------+------------+
// 			//	|       6405 |       7239 |       5325 |        878 |       5334 |       NULL |       NULL |       NULL |
// 			//	+------------+------------+------------+------------+------------+------------+------------+------------+
			
// 			$req_Liste_Modele = $bdd_centreon->prepare('select DISTINCT T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id
// 				FROM service AS S
// 				LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// 				LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// 				LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// 				LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// 				LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// 				LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// 				LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// 				LEFT JOIN on_demand_macro_service AS M on M.svc_svc_id=coalesce(T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id)
// 				WHERE S.Service_id = :ID_Service_Centreon');
// 			$req_Liste_Modele->execute(Array(
// 				'ID_Service_Centreon' => $ID_Service_Centreon
// 			)) or die(print_r($req_Liste_Modele->errorInfo()));
// 			//4) boucle sur les id pour remplir chaque macro
// 			 // on charge l'ensemble des valeur de macro
// 			$Macro = False; // indicateur
// 			$NbMacro = count($Liste_Macro);
// 			addlog("Nbligne_Macro=".$NbMacro);
// 			$Val_Macro = Array();
// 			while ($res_Liste_Modele = $req_Liste_Modele->fetch()) // pour chaque service_id trouvé
// 			{// on recherche les valeurs de macro renseignée avec une boucle sur les 8 service_id
// 				for ($k=0;$k<8;$k++)
// 				{
// 					$svc_svc_id = htmlspecialchars($res_Liste_Modele[$k]);
// 					addlog("svc_svc_id=".$svc_svc_id);
// 					if (($svc_svc_id != NULL) OR ($svc_svc_id != "")) // si le modèle n'est pas null, on traite
// 					{
// //						$req_Macro_Valeur = $bdd_centreon->prepare('SELECT SUBSTR(svc_macro_name, 2, CHAR_LENGTH(svc_macro_name) - 2),svc_macro_value FROM on_demand_macro_service WHERE svc_svc_id= :svc_svc_id');
// 						$req_Macro_Valeur = $bdd_centreon->prepare('SELECT SUBSTR(svc_macro_name, 10, CHAR_LENGTH(svc_macro_name) - 10),svc_macro_value FROM on_demand_macro_service WHERE svc_svc_id= :svc_svc_id');
// 						$req_Macro_Valeur->execute(Array(
// 							'svc_svc_id' => $svc_svc_id
// 						)) or die(print_r($req_Macro_Valeur->errorInfo()));
// 						$res_Macro_Valeur = $req_Macro_Valeur->fetchall();
// /*
// 						echo '<pre>';
// 						print_r($res_Macro_Valeur);
// 						echo '</pre>';
// */
// 						for($j=0;$j<$NbMacro;$j++) // pour chaque Liste_Macro
// 						{
// 							//foreach ($res_Macro_Valeur AS $Macro_Name => $Macro_Valeur) // on boucle sur les valeurs remontée par la requête
// 							foreach ($res_Macro_Valeur AS $Macro_Name) // on boucle sur les valeurs remontée par la requête
// 							{
// /**
// 								addlog("Liste_Macro=".$Liste_Macro[$j]);
// 								addlog("Macro_Name=".$Macro_Name[0]);
// 								addlog("Macro_value=".$Macro_Name[1]);
// */
// 								addlog("Liste_Macro=".$Liste_Macro[$j] . "\n" . "Macro_Name=".$Macro_Name[0] . "\n" . "Macro_value=".$Macro_Name[1]);
// 								//if (($Liste_Macro[$j] == $Macro_Name) AND ($Macro_Valeur != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 								//if (($Liste_Macro[$j] == $Macro_Name[0]) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 								if ((strcasecmp($Liste_Macro[$j], $Macro_Name[0]) == 0) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et Macro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 								// strcasecmp => comparaison insensible à la casse
// 								{
// 									//$Val_Macro[$Macro_Name] = $Macro_Valeur; // tableau nommé
// //									addlog("Val_Macro:".$Macro_Name[0]."=".$Macro_Name[1]);
// // 									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0],8,-1) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// //									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0],8) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// //									$Val_Macro[$Macro_Name[0]] = substr($Macro_Name[0]) . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// 									$Val_Macro[$Macro_Name[0]] = $Macro_Name[0] . ":" . $Macro_Name[1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// 									//											exemple	IFSPEED:1000 
// 									addlog("valeur stockée=". $Val_Macro[$Macro_Name[0]]);
// 								};
// 							};
// 						};
// 					};
// 				};
// 			};
// /*			echo '<pre>';
// 			print_r($res_Liste_Modele);
// 			echo '</pre>'; 
// 			echo '<pre>';
// 			print_r($Val_Macro);
// 			echo '</pre>';
// */
			
			//7) On récupère la consigne éventuelle pour être cohérent avec les arguments classique
			$req_Consigne = $bdd_centreon->prepare('SELECT Consigne_Sonde AS Consigne FROM vInventaireServices WHERE service_id = :ID_Service_Centreon');
			$req_Consigne->execute(Array(
					'ID_Service_Centreon' => $ID_Service_Centreon
			)) or die(print_r($req_Consigne->errorInfo()));
			while ($res_Consigne = $req_Consigne->fetch())
			{
				$Consigne_Sonde = $res_Consigne[0];
			}
			$Chaine_Val_Macro = "";
			foreach($Val_Macro as $Macro_Nom => $Macro_Val)
			{
				$Chaine_Val_Macro .= "!" . $Macro_Val; 
			};
			$Chaine_Val_Macro = substr($Chaine_Val_Macro,1); // stocke les arguments sans le premier !
			$Liste_Argument = $Chaine_Val_Macro . "#" . $Consigne_Sonde;
			$req_C_service = explode("#",$Liste_Argument); // conversion de la chaine en tableau

			// on insère les arguments en base
			$NbLigne = count($req_C_service);
			addlog("Parametres=".$req_C_service[0] . "\nConsigne=".$req_C_service[1] . "\n");
			
			$upd_service = $bdd_supervision->prepare('UPDATE service SET Parametres= :Parametres, Consigne= :Consigne WHERE ID_Demande = :ID_Demande AND ID_Service_Centreon = :ID_Service_Centreon');
			$upd_service->execute(Array(
					'Parametres' => htmlspecialchars($req_C_service[0]),
					'Consigne' => htmlspecialchars($req_C_service[1]),
					'ID_Demande' => $ID_Demande,
					'ID_Service_Centreon' => $ID_Service_Centreon
			)) or die(print_r($upd_service->errorInfo()));
		}else 
		{
			$EST_MACRO = False;
			// récupère les arguments classique du service
			$req_C_service = $bdd_centreon->prepare('SELECT Distinct(Argument) AS Parametres, Consigne_Sonde AS Consigne FROM vInventaireServices WHERE service_id = :ID_Service_Centreon');
			$req_C_service->execute(Array(
					'ID_Service_Centreon' => $ID_Service_Centreon
			)) or die(print_r($req_C_service->errorInfo()));
			
			// on insère les arguments en base
			while ($res_C_service = $req_C_service->fetch())
			{
				$upd_service = $bdd_supervision->prepare('UPDATE service SET Parametres= :Parametres, Consigne= :Consigne WHERE ID_Demande = :ID_Demande AND ID_Service_Centreon = :ID_Service_Centreon');
				$upd_service->execute(Array(
						'Parametres' => htmlspecialchars($res_C_service[0]),
						'Consigne' => htmlspecialchars($res_C_service[1]),
						'ID_Demande' => $ID_Demande,
						'ID_Service_Centreon' => $ID_Service_Centreon
				)) or die(print_r($upd_service->errorInfo()));
			};
		};
	};
	/**
	 * Ramasse miette
	 */
	/**
	 * MAJ periode temporelle vide en attendant de trouver la correction sur la sélection des services
	 */
	$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Nom_Periode="24/24 - 7/7" where Nom_Periode="";');
	$MAJ_Service->execute(Array()) or die(print_r($MAJ_Service->errorInfo()));
//	$num_service++;
};

/*
 * Déprécié le 02/12/14
 * complétement inutile puisque géré dès l'insertion

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//// MAJ Periode_Temporelle
// //Selection des différentes périodes temporelles
// $select_periode = $bdd_supervision->prepare('SELECT DISTINCT(Nom_Periode) FROM Service WHERE ID_Demande= :ID_Demande AND Nom_Periode NOT IN (SELECT Nom_Periode FROM Periode_Temporelle)');
// $select_periode->execute(Array(
// 	'ID_Demande' => $ID_Demande
// )) or die(print_r($select_periode->errorInfo()));
// //$liste_periode = "'"
// while($res_select_periode = $select_periode->fetch())
// {
// //	$liste_periode = $liste_periode . htmlspecialchars($res_select_periode['Nom_Periode']) . "','"
// 	$insert_periode = $bdd_supervision->prepare('INSERT INTO Periode_Temporelle (Nom_Periode, Code_Client) VALUES(:Nom_Periode, :Code_Client)');
// 	$insert_periode->execute(Array(
// 		'Nom_Periode' => htmlspecialchars($res_select_periode['Nom_Periode']),
// 		'Code_Client' => htmlspecialchars($_SESSION['Code_Client'])
// 	)) or die(print_r($insert_periode->errorInfo()));
// }
// //$liste_periode = rtrim($liste_periode,",'");

// Purge de la table Periode_Temporelle
//$Trunc_Periode = $bdd_supervision->prepare('TRUNCATE Table Periode_Temporelle');
//$Trunc_Periode->execute(array()) or die(print_r($Trunc_Periode->errorInfo()));

//selection periode dans Centreon
//$select_plage = $bdd_centreon->prepare('SELECT DISTINCT(Plage_Horaire), Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM vInventaireServices WHERE Plage_Horaire IN (:liste_periode)');
$select_plage = $bdd_centreon->prepare('SELECT DISTINCT(Plage_Horaire), Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM vInventaireServices WHERE Code_Client = :Code_Client');
$select_plage->execute(Array(
	'Code_Client' => htmlspecialchars($_SESSION['Code_Client'])
)) or die(print_r($select_plage->errorInfo()));

$select_plage_dem = $bdd_supervision->prepare('SELECT DISTINCT(Nom_Periode) AS Nom_Periode FROM periode_temporelle WHERE ID_Demande = :ID_Demande');
$select_plage_dem->execute(Array(
	'ID_Demande' => $ID_Demande
)) or die(print_r($select_plage_dem->errorInfo()));
$res_select_plage_dem = $select_plage_dem->fetchAll();

//$NbPlage = count($select_plage_dem);
while($res_select_plage = $select_plage->fetch())
{
	$existant=false;
	foreach ($res_select_plage_dem as $val_plage)
	{
		addlog("comparaison");
		addlog($val_plage[0] == $res_select_plage[0]);
		if ($val_plage[0] == $res_select_plage[0])
		{
			addlog("existant=" . $existant);
			$existant=true;
			addlog("existant=" . $existant);
		};
	};
	if ($existant == false)
	{
*/
	// Mise à jour de l'état des période en fonction de leur sélection
	//	$upd_periode = $bdd_supervision->prepare('UPDATE Periode_Temporelle SET Lundi = :Lun, Mardi = :Mar, Mercredi = :Mer, Jeudi = :Jeu, Vendredi = :Ven, Samedi = :Sam, Dimanche = :Dim WHERE Nom_Periode = :Nom_Periode');
		$insert_periode = $bdd_supervision->prepare('UPDATE periode_temporelle SET Type_Action=if(selection="true","Modifier","NC") WHERE ID_Demande= :id_demande');
		$insert_periode->execute(Array(
			'id_demande' => $ID_Demande
		)) or die(print_r($insert_periode->errorInfo()));
/*
 	};
};
*/
$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur complete table:' . $e->getMessage());
};

///////////////////////////////////////////////////////
// puis on charge les données dans l'onglet paramétrage
///////////////////////////////////////////////////////
