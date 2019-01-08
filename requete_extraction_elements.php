<?php
if (session_id () == '') {
	session_start ();
};
include ('log.php'); // chargement de la fonction de log
include_once ('connexion_sql_centreon.php');
include_once ('connexion_sql_supervision.php');

try 
{
	$bdd_supervision->beginTransaction();
	/**
	 * découpage de l'extraction des éléments
	 * 1 Selection des hôtes de la prestation
	 * 2 Construction de la liste des id_hote
	 * 3 Selection des services de la prestation concernée ET des prestations type "INFRA" à partir de la liste id_hote
	 * 4 Selection des périodes de la prestation concernée ET des prestations type "INFRA" à partir de la liste id_hote
	 */
	
	/**
	 * 1 Selection des hôtes
	 */
	addlog("selection hote pour la prestation " . $prestation . "...");
	
	$req_elements_hote = $bdd_centreon->prepare ( 'SELECT
			 Distinct(Nom_Hote) as Nom_Hote,
			 Hote_Description as Description,
			 IP_Hote as IP_Hote,
			 Controle_Hote as Controle_Hote_Actif,
			 host_id as ID_Hote_Centreon
			FROM vInventaireServices
			WHERE Code_Client= :prestation
			ORDER BY Nom_Hote' );
	$req_elements_hote->execute ( array (
			'prestation' => htmlspecialchars ( $prestation )
	) ) or die ( print_r ( $req_elements_hote->errorInfo () ) );
	addlog("selection hote OK...");
	
	/**
	 * 2 Construction de la liste des id_hote
	 */
	$res_elements_hote = $req_elements_hote->fetchAll ();
	if ($res_elements_hote[0] != "")
	{
	
		$liste_id = "";
		foreach ( $res_elements_hote as $val_hote )
		{
			$liste_id .= "," .$val_hote['ID_Hote_Centreon']; 
		};
		$liste_id = substr($liste_id,1); // chaine construite sans le premier caractère.
		addlog($liste_id);
		
		/**
		 * 3 Selection des services de la prestation concernée et prestation contenant INFRA
		 */
		addlog("selection service...");
		$req_elements_service = $bdd_centreon->prepare ( 'SELECT
				 Distinct(Nom_Hote) as Nom_Hote,
				 IP_Hote as IP_Hote,
				 Controle as Controle_Actif,
				 Sonde as Nom_Service,
				 Argument as Parametres,
				 Consigne_Sonde as Consigne_Service,
				 Frequence,
				 Plage_Horaire as Nom_Periode,
				 host_id as ID_Hote_Centreon,
				 service_id as ID_Service_Centreon,
				 service_modele_id as ID_Modele_Service_Centreon
				FROM vInventaireServices
				WHERE Code_Client= :prestation OR (service_categorie="Systeme" AND host_id IN (' . $liste_id . '))
				ORDER BY Nom_Hote,Sonde' );
		$req_elements_service->execute(array(
				'prestation' => htmlspecialchars($prestation)
			)) or die(print_r($req_elements_service->errorInfo()));
		addlog("selection service... OK");
		
		/**
		 * 4 Selection des périodes de la prestation concernée et prestation contenant INFRA
		 */
		addlog("selection periode...");
		$req_elements_periode = $bdd_centreon->prepare('SELECT
				 Distinct(Plage_Horaire) as Nom_Periode,
				 Lundi,
				 Mardi,
				 Mercredi,
				 Jeudi,
				 Vendredi,
				 Samedi,
				 Dimanche
				FROM vInventaireServices
				WHERE (Code_Client= :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . $liste_id . ')
				ORDER BY Nom_Hote,Sonde');
		$req_elements_periode->execute(array(
				'prestation' => htmlspecialchars($prestation)
			)) or die(print_r($req_elements_periode->errorInfo()));
		addlog("selection periode...OK");
		
		$tbl_hote = "tmp_hote_" . $_SESSION ['ref_tmp_extract'];
		$tbl_service = "tmp_service_" . $_SESSION ['ref_tmp_extract'];
		$tbl_periode = "tmp_periode_" . $_SESSION ['ref_tmp_extract'];
		
		
		/**
		 *  suppression des tables temporaires si elles existent
		 */
		addlog("purge tables tmp existantes...");
		$DROP_tmp_hote = $bdd_supervision->query ('DROP TABLE IF EXISTS ' . $tbl_hote . '');
		$DROP_tmp_service = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_service . '');
		$DROP_tmp_periode = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_periode . '');
		/**
		 *  création des tables temporaires pour formalisation
		 */
		addlog("création table " . $tbl_hote);
		$CRE_tmp_hote = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_hote . ' (
		 `ID_Hote` int(11) NOT NULL AUTO_INCREMENT,
		 `ID_Demande` int(11) NOT NULL,
		 `ID_Hote_Centreon` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
		 `Nom_Hote` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		 `Description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		 `IP_Hote` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
		 `Type_Hote` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		 `ID_Localisation` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		 `OS` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		 `Architecture` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
		 `Langue` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
		 `Fonction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		 `Controle_Actif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		 `Commentaire` text COLLATE utf8_unicode_ci NOT NULL,
		 `Consigne` text COLLATE utf8_unicode_ci NOT NULL,
		 `Detail_Consigne` text COLLATE utf8_unicode_ci NOT NULL,
		 `Type_Action` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		 `Etat_Parametrage` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		 `selection` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
		 PRIMARY KEY (`ID_Hote`),
		 UNIQUE KEY `Nom_Hote` (`Nom_Hote`,`ID_Demande`,`IP_Hote`))
		 ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' );
		
		addlog("création table " . $tbl_service);
		$CRE_tmp_service = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_service . ' (
		`ID_Service` int(11) NOT NULL AUTO_INCREMENT,
		`ID_Demande` int(11) NOT NULL,
		`ID_Hote` int(11) NOT NULL,
		`ID_Service_Centreon` int(11) NOT NULL,
		`ID_Hote_Centreon` int(11) NOT NULL,
		`ID_Modele_Service` int(11) NOT NULL,
		`ID_Modele_Service_Centreon` int(11) NOT NULL,
		`Nom_Service` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`Nom_Hote` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		`Parametres` text COLLATE utf8_unicode_ci NOT NULL,
		`Nom_Periode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`ID_Periode_Temporelle` int(11) NOT NULL,
		`Frequence` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
		`Consigne` text COLLATE utf8_unicode_ci NOT NULL,
		`Detail_Consigne` text COLLATE utf8_unicode_ci NOT NULL,
		`Type_Action` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`Etat_Parametrage` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`Controle_Actif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		`Commentaire` text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`ID_Service`),
		UNIQUE KEY `Nom_Service` (`Nom_Service`,`Nom_Hote`,`ID_Demande`,`ID_Hote`,`ID_Hote_Centreon`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' );
		
		addlog("création table " . $tbl_periode);
		$CRE_tmp_periode = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_periode . ' (
		  `Id_Periode_Temporelle` int(11) NOT NULL AUTO_INCREMENT,
		  `ID_Demande` int(11) NOT NULL,
		  `Commentaire` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `Nom_Periode` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Lundi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Mardi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Mercredi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Jeudi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Vendredi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Samedi` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Dimanche` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
		  `Type_Action` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		  `Etat_Parametrage` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`Id_Periode_Temporelle`),
		  UNIQUE KEY `Nom_Periode` (`Nom_Periode`,`ID_Demande`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' );
		
		/**
		 *  insertion des données dans chacune des tables temporaires
		 */
		$Ref_Hote="";
		$Ref_Service="";
		$Ref_Periode="";
		
		/**
		 * Traitement de la liste des hôtes avant insertion
		 * Création de la chaine VALUES pour limiter les insert
		 */
		addlog("traitement hote...");
		$value_hote = "";
		$i=1;
		foreach ( $res_elements_hote as $res_elements )
		{
			$Localisation = stristr ( $res_elements ['Nom_Hote'], '-', TRUE ); // récupère la localisation => les caractères avant le premier tiret
			$Type = stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-', TRUE ); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
			$Nom_Hote = substr ( stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-' ), 1 ); // enlève localisation et type
		
			$value_hote .= ",('" . $Nom_Hote . "'," . $res_elements['ID_Hote_Centreon'] . ",\"" . $res_elements['Description'] . "\",'" . $res_elements['IP_Hote'] . "','" . $res_elements['Controle_Hote_Actif'] . "','" . $Type . "','" . $Localisation . "')";
		
			if ($i % 100 == 0)
			{
				addlog("insertion hote partielle " . $i/100 . "...");
				$value_hote = substr($value_hote,1); // suppression de la première virgule
				addlog($value_hote);
				$insert_hote_liste = $bdd_supervision->prepare (
						'INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
				$insert_hote_liste->execute(array()) or die(print_r($insert_hote_liste->errorInfo()));
				$value_hote = "";
			};
			$i++;
		};
		addlog("insertion hote...finale");
		$value_hote = substr($value_hote,1); // suppression de la première virgule
		addlog($value_hote);
		$insert_hote_liste = $bdd_supervision->prepare ( 
			'INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
		$insert_hote_liste->execute(array()) or die(print_r($insert_hote_liste->errorInfo()));
		addlog("traitement hote...OK");
			
		/**
		 * Traitement de la liste des services avant insertion
		 * Création de la chaine VALUES pour un seul insert
		 */
		addlog("traitement service...");
		$value_service = "";
		$i=1;
		 while ( $res_elements = $req_elements_service->fetch())
		{
			$Nom_Hote = substr ( stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-' ), 1 ); // enlève localisation et type
			if ($res_elements ['ID_Modele_Service_Centreon'] == NULL)
			{
				$ID_Modele_Service_Centreon = 0;
			} else 
			{
				$ID_Modele_Service_Centreon = $res_elements ['ID_Modele_Service_Centreon'];
			};
			/**
			 * Traitement des ' => remplacement par _SQUOTE_ dans le champ Parametre
			 */
				$res_elements ['Parametres']=str_replace("'","_SQUOTE_",$res_elements ['Parametres']);
					
			$value_service .= ",('" . $res_elements ['Nom_Service'] . "','" . $res_elements ['Frequence'] . "','" . $res_elements ['Nom_Periode'] . "','" . $res_elements ['Controle_Actif'] . "'," . $res_elements ['ID_Service_Centreon'] . "," . $res_elements ['ID_Hote_Centreon'] . "," . $ID_Modele_Service_Centreon . ",'" . $res_elements ['Consigne_Service'] . "','" . $Nom_Hote . "','" . substr($res_elements ['Parametres'],1) . "')";
			
			if ($i % 500 == 0)
			{
	
				addlog("insertion service partielle " . $i/500 . "...");
				$value_service = substr($value_service,1); // suppression de la première virgule
				addlog($value_service);
				addlog('INSERT INTO ' . $tbl_service . ' (Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon, ID_Modele_Service_Centreon, Consigne, Nom_Hote, Parametres) VALUES ' . $value_service . '');
				$insert_service_selec = $bdd_supervision->prepare ( 'INSERT INTO ' . $tbl_service . ' (Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon, ID_Modele_Service_Centreon, Consigne, Nom_Hote, Parametres)
					 VALUES ' . $value_service . '');
				$insert_service_selec->execute(array()) or die (print_r( $insert_service_selec->errorInfo()));
				$value_service = "";
			};
			$i++;
		};
		addlog("insertion service finale...");
		$value_service = substr($value_service,1); // suppression de la première virgule
		addlog($value_service);
		
		$insert_service_selec = $bdd_supervision->prepare ( 'INSERT INTO ' . $tbl_service . ' (Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon, ID_Modele_Service_Centreon, Consigne, Nom_Hote, Parametres)
			 VALUES ' . $value_service . '');
		$insert_service_selec->execute(array()) or die (print_r( $insert_service_selec->errorInfo()));
		addlog("traitement service...OK");
			
		
		/**
		 * Traitement de la liste des periode avant insertion
		 * Création de la chaine VALUES pour un seul insert
		 */
			addlog("traitement periode...");
		$value_periode = "";
		while ( $res_elements = $req_elements_periode->fetch())
		{
			$value_periode .= ",('" . $res_elements ['Nom_Periode'] . "','" . $res_elements ['lundi'] . "','" . $res_elements ['mardi'] . "','" . $res_elements ['mercredi'] . "','" . $res_elements ['jeudi'] . "','" . $res_elements ['vendredi'] . "','" . $res_elements ['samedi'] . "','" . $res_elements ['dimanche'] . "')";
		};
		addlog("insertion periode...");
		$value_periode = substr($value_periode,1); // suppression de la première virgule
			$insert_periode_liste = $bdd_supervision->prepare ( 'INSERT IGNORE INTO ' . $tbl_periode . ' (Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche)
				 VALUES ' . $value_periode . '');
			$insert_periode_liste->execute(array()) or die(print_r($insert_periode_liste->errorInfo()));
			addlog("traitement periode...OK");
			
		/**
		 * traitement des données
		 */
		
		$liste_hote_extract = $bdd_supervision->prepare ( 'SELECT ID_Hote_Centreon FROM ' . $tbl_hote . '' );
		$liste_hote_extract->execute ( Array () ) or die ( print_r ( $liste_hote_extract->errorInfo () ) );
		
		/**
		 *  on boucle sur la liste des hôte pour MAJ table
		 */
		while ( $res_liste_hote_extract = $liste_hote_extract->fetch () ) {
			$ID_Hote_Centreon = htmlspecialchars ( $res_liste_hote_extract ['ID_Hote_Centreon'] );
			$select_categ_hote = $bdd_centreon->prepare ( 'SELECT host_name, hc_name, hc_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name' );
			
			$select_categ_hote->execute ( Array (
					'ID_Hote' => $ID_Hote_Centreon 
			) ) or die ( print_r ( $select_categ_hote->errorInfo () ) );
			$Cat_Archi = "";
			$Cat_OS = "";
			$Cat_Langue = "";
			$Cat_Type = "";
			$Cat_Fonction = "";
			
			while ( $res_select_categ_hote = $select_categ_hote->fetch () ) {
				$categorie = stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', TRUE ); // récupère la chaine AVANT le _
				if ($categorie == "Architecture") // si categorie est "Archi"
				{
					$Cat_Archi = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
				} else if ($categorie == "OS") // si categorie est "OS"
				{
					$Cat_OS = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
				} else if ($categorie == "Langue") // si categorie est "Langue"
				{
					$Cat_Langue = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
				} else if ($categorie == "Type") // si categorie est "Type"
				{
					$Cat_Type = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
				} else if ($categorie == "Fonction") // si categorie est "Fonction"
				{
					$Cat_Fonction = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
				} else if ($categorie != "") // si categorie n'est pas vide
				{
					$Cat_Inconnue = stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', TRUE ); // récupère la chaine AVANT le _
					echo "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge";
					addlog ( "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge" );
					Return False;
				};
			};
			$MAJ_Hote = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_hote . ' SET Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon' );
			$MAJ_Hote->execute ( Array (
					'Cat_Fonction' => $Cat_Fonction,
					'Cat_Archi' => $Cat_Archi,
					'Cat_OS' => $Cat_OS,
					'Cat_Langue' => $Cat_Langue,
					'ID_Hote_Centreon' => $ID_Hote_Centreon 
			) ) or die ( print_r ( $MAJ_Hote->errorInfo () ) );
		};
		
		/**
		 *  liste des services de la demande
		 */
		$liste_service_demande = $bdd_supervision->prepare ( 'SELECT ID_Service_Centreon, ID_Hote_Centreon, Consigne FROM ' . $tbl_service . '' );
		$liste_service_demande->execute ( Array () ) or die ( print_r ( $liste_service_demande->errorInfo () ) );
		
		/**
		 *  on boucle sur la liste des services pour MAJ table
		 */
		addlog("MAJ modele service");
		$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_service . ' AS S INNER JOIN relation_modeles AS RM ON S.ID_Modele_Service_Centreon=RM.ID_Modele_Service_Centreon SET S.ID_Modele_Service=RM.ID_Modele_Service' );
		$upd_service->execute ( Array () ) or die ( print_r ( $upd_service->errorInfo () ) );
		addlog("MAJ modele_service OK.");
	
		while ( $res_liste_service_demande = $liste_service_demande->fetch () ) {
			$ID_Service_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Service_Centreon'] );
			$ID_Hote_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Hote_Centreon'] );
			$Consigne_Sonde = htmlspecialchars ( $res_liste_service_demande ['Consigne'] );
			
			/**
			 *  met à jour le modèle de service Changement
			 */
			
			// vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
			$req_type_modele = $bdd_supervision->prepare ( 'SELECT MS.MS_EST_MACRO FROM ' . $tbl_service . ' AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Service_Centreon = :ID_Service_Centreon' );
			$req_type_modele->execute ( Array (
					'ID_Service_Centreon' => $ID_Service_Centreon 
			) ) or die ( print_r ( $req_type_modele->errorinfo () ) );
			
			while ( $res_type_modele = $req_type_modele->fetch () ) // ne doit retourner qu'un seul enregistrement
			{
				if ($res_type_modele [0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
				{
					/**
					 *  appel procédure commune de traitement des macros
					 */
					include('requete_traitement_macros.php');
					
					/**
					 *  7) On construit la chaine des Macro selon le modèle des arguments
					 */
					$Chaine_Val_Macro = "";
					foreach ( $Val_Macro as $Macro_Nom => $Macro_Val )
					{
						$Chaine_Val_Macro .= "!" . $Macro_Val;
					};
					$Chaine_Val_Macro = substr($Chaine_Val_Macro,1); // stocke les arguments sans le premier !
					$Liste_Argument = $Chaine_Val_Macro . "#" . $Consigne_Sonde;
					$req_C_service = explode ( "#", $Liste_Argument ); // conversion de la chaine en tableau
					                                               
					/**
					 *  on insère les arguments en base
					 */
					$NbLigne = count ( $req_C_service );
					addlog("argument_service_id".$ID_Service_Centreon."=". htmlspecialchars($req_C_service[0]),1);
					$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_service . ' SET Parametres= :Parametres WHERE ID_Service_Centreon = :ID_Service_Centreon' );
					$upd_service->execute ( Array (
							'Parametres' => htmlspecialchars($req_C_service[0]),
							'ID_Service_Centreon' => $ID_Service_Centreon 
					) ) or die ( print_r ( $upd_service->errorInfo () ) );
				};
			};
		};
		
		/**
		 *  Selection des éléments hôte
		 */
		$SEL_tmp_hote = $bdd_supervision->prepare ( 'SELECT
				 Nom_Hote,
				 Description,
				 IP_Hote,
				 Controle_Actif,
				 ID_Hote_Centreon,
				 Type_Hote,
				 ID_Localisation,
				 OS,
				 Architecture,
				 Langue,
				 Fonction
				 FROM ' . $tbl_hote . '' );
		$SEL_tmp_hote->execute ( Array () ) or die ( print_r ( $SEL_tmp_hote->errorInfo () ) );
		$r_hote = $SEL_tmp_hote->fetchAll ();
		$Nb_Hote = count ( $r_hote );
		
		/**
		 *  Sélection des éléments service
		 */
		$SEL_tmp_service = $bdd_supervision->prepare ( 'SELECT
				 DISTINCT(S.Nom_Service) AS Nom_Service,
					S.Nom_Hote AS Nom_Hote,
					H.IP_Hote AS IP_Hote,
					H.ID_Localisation AS ID_Localisation,
					S.Nom_Periode AS Nom_Periode,
					S.Frequence AS Frequence,
					S.Consigne AS Consigne,
					S.Controle_Actif AS Controle_Actif,
					MS.Modele_Service AS MS_Modele_Service,
					MS.MS_Libelles AS MS_Libelles,
					S.Parametres AS Parametres,
					S.Detail_Consigne AS Detail_Consigne,
					S.Type_Action AS Type_Action,
					S.Etat_Parametrage AS Etat_Parametrage,
					S.ID_Service AS ID_Service,
					S.Commentaire AS Commentaire,
					MS.MS_Description AS MS_Description,
					MS.MS_Arguments AS MS_Arguments,
					MS.MS_Macro AS MS_Macro,
					MS.MS_EST_MACRO AS MS_EST_MACRO,
					H.ID_Hote AS ID_Hote
			FROM ((' . $tbl_service . ' AS S
				LEFT JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service)
				LEFT JOIN ' . $tbl_hote . ' AS H ON S.ID_Hote_Centreon=H.ID_Hote_Centreon)
			ORDER BY H.ID_Localisation, S.Nom_Hote, S.Nom_Service');
		$SEL_tmp_service->execute ( Array () ) or die ( print_r ( $SEL_tmp_service->errorInfo () ) );
		
		// Selection de tous les services de la demande
		// Detail de la requête
		// Nom_Service 0
		// Nom_Hote 1
		// IP_Hote 2
		// ID_Localisation 3
		// Nom_Periode 4
		// Frequence 5
		// Consigne 6
		// Controle_Actif 7
		// MS_Modele_Service 8
		// MS_Libelles 9
		// Parametres 10
		// Detail_Consigne 11
		// Type_Action 12
		// Etat_Parametrage 13
		// ID_Service 14
		// Commentaire 15
		// MS_Description 16
		// MS_Arguments 17
		// MS_Macro 18
		// MS_EST_MACRO 19
		// ID_Hote 20
		$r_service = $SEL_tmp_service->fetchAll ();
		
		$nb_service = count ( $r_service );
		
		$liste_service = "";
		$NbFieldset_Service = 1;
		
		/**
		 *  Sélection des éléments periode
		 */
		$SEL_tmp_periode = $bdd_supervision->prepare ( 'SELECT Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM ' . $tbl_periode . '' );
		$SEL_tmp_periode->execute ( Array () ) or die ( print_r ( $SEL_tmp_periode->errorInfo () ) );
		
		$r_plage = $SEL_tmp_periode->fetchAll ();
		
		$nb_plage = count ( $r_plage );
		$bdd_supervision->commit();
	} else
	{
		addlog("KO - aucun élément trouvé pour la prestation[" . $prestation . "].");
		echo "<p>Aucun élément n'a été trouvé pour cette prestation.</p>";
		$bdd_supervision->commit();
		exit;
	};
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur requete_extraction_elements: ' . $e->getMessage());
};
