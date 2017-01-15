<?php
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


//////////////////////////////////////////////////////////////////////////////////////////////////////////
//liste des hôtes de la demande
addlog("COMPLETE_TABLE: Traitement des hotes...");
try {
	$bdd_supervision->beginTransaction();
	$liste_hote_demande = $bdd_supervision->prepare('SELECT ID_Hote_Centreon FROM hote WHERE ID_Demande = :ID_Demande');
	$liste_hote_demande->execute(Array(
			'ID_Demande' => $ID_Demande)) or die(print_r($liste_hote_demande->errorInfo()));
	
	// on boucle sur la liste des hôte pour MAJ table
	while ($res_liste_hote_demande = $liste_hote_demande->fetch())
	{
		$ID_Hote_Centreon = htmlspecialchars($res_liste_hote_demande['ID_Hote_Centreon']);
			addlog("Selection categories");
		$select_categ_hote = $bdd_centreon->prepare('SELECT host_name, hc_name, HC_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name');
	
		$select_categ_hote->execute(Array(
		'ID_Hote' => $ID_Hote_Centreon)) or die(print_r($select_categ_hote->errorInfo()));
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
			};
		};
		$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Type_Hote= :Type_Hote, ID_Localisation= :ID_Localisation, Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon AND ID_Demande= :ID_Demande');
		$MAJ_Hote->execute(Array(
			'Type_Hote' => $Type,
			'ID_Localisation' => $Localisation,
			'Cat_Fonction' => $Cat_Fonction,
			'Cat_Archi' => $Cat_Archi,
			'Cat_OS' => $Cat_OS,
			'Cat_Langue' => $Cat_Langue,
			'ID_Hote_Centreon' => $ID_Hote_Centreon,
			'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Hote->errorInfo()));
		addlog("champs table hote mis à jour!");
		echo "champs table hote mis à jour.";
	};

	/**
	 *  mise à jour du type d'action à effecuer pour filtrer l'affichage sur l'onglet paramétrage
	 *  => si selection=true et si controle_actif=actif => Modifier sinon Activer
	 *  => si selection=false et si type_action est vide => NC sinon pas de changement
	 */
// 15/01/2017: probable bug hôtes non affichés sur reprise
//	$MAJ_Hote2 = $bdd_supervision->prepare('UPDATE hote SET type_action=if(selection="true",if(Controle_Actif="actif","Modifier","Activer"),"NC"), etat_parametrage="Brouillon" WHERE ID_Demande= :id_demande');
	$MAJ_Hote2 = $bdd_supervision->prepare('UPDATE hote SET type_action=if(selection="true",if(Controle_Actif="actif","Modifier","Activer"),if(type_action="","NC",type_action), etat_parametrage="Brouillon" WHERE ID_Demande= :id_demande');
	$MAJ_Hote2->execute(Array(
			'id_demande' => $ID_Demande
	)) or die(print_r($MAJ_Hote2->errorInfo()));

	/**
	 * liste des services de la demande
	 */
	addlog("COMPLETE_TABLE: Traitement des services...");
	$liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Hote FROM service WHERE ID_Demande = :ID_Demande AND ID_Service_Centreon <>0'); // dans certains cas non reproduis pour l'instant, vérole les paramètres du service car ne trouve pas les infos dans la base centreon pour id=0
	$liste_service_demande->execute(Array(
		'ID_Demande' => $ID_Demande
	)) or die(print_r($liste_service_demande->errorInfo()));
	/**
	 *  on boucle sur la liste des services pour MAJ table
	 */
	while ($res_liste_service_demande = $liste_service_demande->fetch())
	{
		$ID_Service_Centreon = htmlspecialchars($res_liste_service_demande['ID_Service_Centreon']);
		addlog("ID_Service_Centreon=".$ID_Service_Centreon);
		$ID_Hote_Centreon = htmlspecialchars($res_liste_service_demande['ID_Hote_Centreon']);
		addlog("ID_Hote_Centreon=".$ID_Hote_Centreon);
		$Nom_Hote = htmlspecialchars($res_liste_service_demande['Nom_Hote']);
		addlog("Nom_Hote=".$Nom_Hote);
		
		addlog("récupération des infos services");
	 	if ($ID_Hote_Centreon != 0)
		{
			/**
			 *  mise à jour de l'ID_Hote à partir de l'ID_Hote_Centreon s'il existe
			 */
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
		};
		/**
		 *  met à jour le modèle de service Centreon
		 */
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
		
		/**
		 *  met à jour le modèle de service Changement
		 */
		$upd_service = $bdd_supervision->prepare('UPDATE service AS S INNER JOIN relation_modeles AS RM ON S.ID_Modele_Service_Centreon=RM.ID_Modele_Service_Centreon SET S.ID_Modele_Service=RM.ID_Modele_Service WHERE ID_Demande = :ID_Demande');
		$upd_service->execute(Array(
			'ID_Demande' => $ID_Demande
		)) or die(print_r($upd_service->errorInfo()));
	
		/**
		 *  vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
		 */
		$req_type_modele = $bdd_supervision->prepare('SELECT MS.MS_EST_MACRO FROM service AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Service_Centreon = :ID_Service_Centreon AND ID_Demande = :ID_Demande');
		$req_type_modele->execute(Array(
			'ID_Service_Centreon' => $ID_Service_Centreon,
			'ID_Demande' => $ID_Demande
		)) or die(print_r($req_type_modele->errorinfo()));
		
		while ($res_type_modele = $req_type_modele->fetch()) // ne doit retourner qu'un seul enregistrement
		{
			if ($res_type_modele[0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
			{
				/**
				 *  appel procédure commune de traitement des macros
				 */
				include('requete_traitement_macros.php');
				
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
							'Parametres' => htmlspecialchars(substr($res_C_service[0],1)),
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
		$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET etat_parametrage="Brouillon" where ID_Demande= :ID_Demande;');
		$MAJ_Service->execute(Array(
				'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Service->errorInfo()));
		
		/**
		 * MAJ periode temporelle vide en attendant de trouver la correction sur la sélection des services
		 * Désactivé le 30/01/16
		$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Nom_Periode="24/24 - 7/7" where Nom_Periode="";');
		$MAJ_Service->execute(Array()) or die(print_r($MAJ_Service->errorInfo()));
		*/
	
	};
	/**
	 *  Mise à jour de l'état des période en fonction de leur sélection
	 */
	$insert_periode = $bdd_supervision->prepare('UPDATE periode_temporelle SET Type_Action=if(selection="true","Modifier","NC"), etat_parametrage="Brouillon" WHERE ID_Demande= :id_demande');
	$insert_periode->execute(Array(
		'id_demande' => $ID_Demande
	)) or die(print_r($insert_periode->errorInfo()));
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur complete table:' . $e->getMessage());
};