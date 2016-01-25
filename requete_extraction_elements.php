<?php
if (session_id () == '') {
	session_start ();
};
include ('log.php'); // chargement de la fonction de log
//addlog("connexion centreon...");
include_once ('connexion_sql_centreon.php');
//addlog("connexion centreon OK.");
// addlog("connexion supervision...");
include_once ('connexion_sql_supervision.php');
// addlog("connexion supervision OK.");

try {
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
		//while ($res_elements_hote = $req_elements_hote->fetch())
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
				WHERE (Code_Client= :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . $liste_id . ')
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
		
		// suppression des tables temporaires si elles existent
		// addlog("suppression tables tmp...");
		$DROP_tmp_hote = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_hote . '' );
		$DROP_tmp_service = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_service . '' );
		$DROP_tmp_periode = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_periode . '' );
		// addlog("suppression table tmp OK.");
		
		// création des tables temporaires pour formalisation
		// addlog("Creation table tmp_hote...");
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
		
		// addlog("Creation table tmp_service...");
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
		
		// addlog("Creation table tmp_periode...");
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
		
		// insertion des données dans chacune des tables temporaires
		// addlog("insertion dans chaque table...");
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
		//while ( $res_elements = $req_elements_hote->fetch())
		foreach ( $res_elements_hote as $res_elements )
		{
			$Localisation = stristr ( $res_elements ['Nom_Hote'], '-', TRUE ); // récupère la localisation => les caractères avant le premier tiret
			$Type = stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-', TRUE ); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
			$Nom_Hote = substr ( stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-' ), 1 ); // enlève localisation et type
		
		//	$value_hote .= ",(\"" . $Nom_Hote . "\",\"" . $res_elements['ID_Hote_Centreon'] . "\",\"" . $res_elements['Description'] . "\",\"" . $res_elements['IP_Hote'] . "\",\"" . $res_elements['Controle_Hote_Actif'] . "\",\"" . $Type . "\",\"" . $Localisation . "\")";
			$value_hote .= ",('" . $Nom_Hote . "'," . $res_elements['ID_Hote_Centreon'] . ",'" . $res_elements['Description'] . "','" . $res_elements['IP_Hote'] . "','" . $res_elements['Controle_Hote_Actif'] . "','" . $Type . "','" . $Localisation . "')";
		
			if ($i % 100 == 0)
			{
				addlog("insertion hote partielle " . $i/100 . "...");
				$value_hote = substr($value_hote,1); // suppression de la première virgule
				addlog($value_hote);
				$insert_hote_liste = $bdd_supervision->prepare (
						'INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
				//	addlog('INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
				$insert_hote_liste->execute(array()) or die(print_r($insert_hote_liste->errorInfo()));
				$value_hote = "";
			};
			$i++;
			//addlog($value_hote);
		};
		addlog("insertion hote...finale");
		$value_hote = substr($value_hote,1); // suppression de la première virgule
		addlog($value_hote);
		$insert_hote_liste = $bdd_supervision->prepare ( 
			'INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
		//	addlog('INSERT INTO ' . $tbl_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation) VALUES ' . $value_hote . '');
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
			//VALUES(:Nom_Service, :Frequence, :Nom_Periode, :Controle_Actif, :ID_Service_Centreon, :ID_Hote_Centreon, :ID_Modele_Service_Centreon, :consigne, :nom_hote, :parametres)
			//$value_service .= ",('" . $res_elements ['Nom_Service'] . "','" . $res_elements ['Frequence'] . "','" . $res_elements ['Nom_Periode'] . "','" . $res_elements ['Controle_Actif'] . "'," . $res_elements ['ID_Service_Centreon'] . "," . $res_elements ['ID_Hote_Centreon'] . "," . $ID_Modele_Service_Centreon . ",'" . $res_elements ['Consigne_Service'] . "','" . $Nom_Hote . "',\"" . substr($res_elements ['Parametres'],1) . "\")";
			/**
			 * Traitement des ' => remplacement par _SQUOTE_ dans le champ Parametre
			 */
				addlog("avant=" . $value_service);
				$res_elements ['Parametres']=str_replace("'","_SQUOTE_",$res_elements ['Parametres']);
				addlog("apres=" . $value_service);
					
			$value_service .= ",('" . $res_elements ['Nom_Service'] . "','" . $res_elements ['Frequence'] . "','" . $res_elements ['Nom_Periode'] . "','" . $res_elements ['Controle_Actif'] . "'," . $res_elements ['ID_Service_Centreon'] . "," . $res_elements ['ID_Hote_Centreon'] . "," . $ID_Modele_Service_Centreon . ",'" . $res_elements ['Consigne_Service'] . "','" . $Nom_Hote . "','" . substr($res_elements ['Parametres'],1) . "')";
			
			if ($i % 500 == 0)
			{
	// 		/**
	// 		 * Traitement des Antislash => remplacement par _BCKSL_ ==>> en cours de test voir pour privilégier les / au lieu des \ dans les arguments 
	// 		 */
	// 			addlog("avant=" . $value_service);
	// 			$value_service=str_replace("\\","_BCKSL_",$value_service);
	// 			addlog("apres=" . $value_service);
	
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
		
// 		/**
// 		 * Traitement des Antislash => remplacement par _BCKSL_ ==>> en cours de test voir pour privilégier les / au lieu des \ dans les arguments 
// 		 */
// 			addlog("avant=" . $value_service);
// 			$value_service=str_replace("\\","_BCKSL_",$value_service);
// 			addlog("apres=" . $value_service);

		//addlog('INSERT INTO ' . $tbl_service . ' (Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon, ID_Modele_Service_Centreon, Consigne, Nom_Hote, Parametres) VALUES ' . $value_service . '');
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
			//VALUES(:Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche)' );
			$value_periode .= ",('" . $res_elements ['Nom_Periode'] . "','" . $res_elements ['lundi'] . "','" . $res_elements ['mardi'] . "','" . $res_elements ['mercredi'] . "','" . $res_elements ['jeudi'] . "','" . $res_elements ['vendredi'] . "','" . $res_elements ['samedi'] . "','" . $res_elements ['dimanche'] . "')";
			//addlog($value_periode);
		};
		addlog("insertion periode...");
		$value_periode = substr($value_periode,1); // suppression de la première virgule
			$insert_periode_liste = $bdd_supervision->prepare ( 'INSERT IGNORE INTO ' . $tbl_periode . ' (Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche)
				 VALUES ' . $value_periode . '');
			$insert_periode_liste->execute(array()) or die(print_r($insert_periode_liste->errorInfo()));
			addlog("traitement periode...OK");
			
		// ///////////////////////////////////////////////////////////////////////////////////////
		// traitement des données
		// ///////////////////////////////////////////////////////////////////////////////////////
		
		// addlog("selection ID_Hote_Centreon dans tmp_hote");
		$liste_hote_extract = $bdd_supervision->prepare ( 'SELECT ID_Hote_Centreon FROM ' . $tbl_hote . '' );
		$liste_hote_extract->execute ( Array () ) or die ( print_r ( $liste_hote_extract->errorInfo () ) );
		// addlog("selection ID_hote_centreon OK.");
		
		// on boucle sur la liste des hôte pour MAJ table
		// $i = 1;
		// addlog("boucle sur chaque hote de la table tmp_hote");
		while ( $res_liste_hote_extract = $liste_hote_extract->fetch () ) {
			// addlog("passe n°". $i);
			$ID_Hote_Centreon = htmlspecialchars ( $res_liste_hote_extract ['ID_Hote_Centreon'] );
		// 	addlog("Selection categories");
			// $select_categ_hote = $bdd_centreon->prepare('SELECT Distinct(host_name), hc_name, HC_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name');
			$select_categ_hote = $bdd_centreon->prepare ( 'SELECT host_name, hc_name, hc_alias FROM ((host as H LEFT JOIN hostcategories_relation AS HCR ON H.host_id=HCR.host_host_id) LEFT JOIN hostcategories AS HC ON HCR.hostcategories_hc_id=HC.hc_id) WHERE H.host_id= :ID_Hote ORDER BY H.host_name' );
			
			$select_categ_hote->execute ( Array (
					'ID_Hote' => $ID_Hote_Centreon 
			) ) or die ( print_r ( $select_categ_hote->errorInfo () ) );
			// extraction
			$Cat_Archi = "";
			$Cat_OS = "";
			$Cat_Langue = "";
			$Cat_Type = "";
			$Cat_Fonction = "";
			
		// 	addlog("boucle d'extraction de chaque categorie...");
			while ( $res_select_categ_hote = $select_categ_hote->fetch () ) {
				/*
				 * le 25/11/14 inutile puisque calculé plus haut
				 * if ($Localisation == "")
				 * {
				 * $Localisation = stristr(htmlspecialchars($res_select_categ_hote['host_name']),'-',TRUE); // récupère la localisation => les caractères avant le premier tiret
				 * addlog($Localisation);
				 * };
				 * if ($Type == "")
				 * {
				 * $Type = stristr(substr(stristr(htmlspecialchars($res_select_categ_hote['host_name']),'-'),1),'-',TRUE); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
				 * addlog($Type);
				 * };
				 */
				$categorie = stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', TRUE ); // récupère la chaine AVANT le _
				if ($categorie == "Architecture") // si categorie est "Archi"
				{
					$Cat_Archi = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
					// addlog($Cat_Archi);
				} else if ($categorie == "OS") // si categorie est "OS"
				{
					$Cat_OS = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
					// addlog($Cat_OS);
				} else if ($categorie == "Langue") // si categorie est "Langue"
				{
					$Cat_Langue = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
					// addlog($Cat_Langue);
				} else if ($categorie == "Type") // si categorie est "Type"
				{
					$Cat_Type = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
					// addlog($Cat_Type);
				} else if ($categorie == "Fonction") // si categorie est "Fonction"
				{
					$Cat_Fonction = substr ( stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', FALSE ), 1 ); // récupère la chaine APRES le _
					// addlog($Cat_Fonction);
				} else if ($categorie != "") // si categorie n'est pas vide
				{
					$Cat_Inconnue = stristr ( htmlspecialchars ( $res_select_categ_hote ['hc_name'] ), '_', TRUE ); // récupère la chaine AVANT le _
					echo "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge";
					addlog ( "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge" );
					Return False;
				};
			};
		// 	addlog("boucle extraction categorie OK.");
			
			// modif le 03-11-2014
		// 	addlog("MAJ Table hote selon les categories extraites");
			// $MAJ_Hote = $bdd_supervision->prepare('UPDATE ' . $tbl_hote . ' SET Type_Hote= :Type_Hote, ID_Localisation= :ID_Localisation, Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon');
			$MAJ_Hote = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_hote . ' SET Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon' );
			$MAJ_Hote->execute ( Array (
					
					// 'Type_Hote' => $Type,
					// 'ID_Localisation' => $Localisation,
					'Cat_Fonction' => $Cat_Fonction,
					'Cat_Archi' => $Cat_Archi,
					'Cat_OS' => $Cat_OS,
					'Cat_Langue' => $Cat_Langue,
					'ID_Hote_Centreon' => $ID_Hote_Centreon 
			) ) or die ( print_r ( $MAJ_Hote->errorInfo () ) );
			// addlog("champs table hote mis à jour!");
			// echo "champs table hote mis à jour.";
		};
		// addlog("boucle hote terminee OK.");
		
		// ////////////////////////////////////////////////////////////////////////////////////////////////////////
		// liste des services de la demande
		// $liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Hote FROM ' . $tbl_service . '');
		// addlog("selection service dans tmp_service");
		$liste_service_demande = $bdd_supervision->prepare ( 'SELECT ID_Service_Centreon, ID_Hote_Centreon, Consigne FROM ' . $tbl_service . '' );
		$liste_service_demande->execute ( Array () ) or die ( print_r ( $liste_service_demande->errorInfo () ) );
		
		// on boucle sur la liste des services pour MAJ table
		// addlog("boucle sur chaque service de la table tmp_service");
		addlog("MAJ modele service");
		$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_service . ' AS S INNER JOIN relation_modeles AS RM ON S.ID_Modele_Service_Centreon=RM.ID_Modele_Service_Centreon SET S.ID_Modele_Service=RM.ID_Modele_Service' );
		$upd_service->execute ( Array () ) or die ( print_r ( $upd_service->errorInfo () ) );
		addlog("MAJ modele_service OK.");
	
		while ( $res_liste_service_demande = $liste_service_demande->fetch () ) {
			$ID_Service_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Service_Centreon'] );
			$ID_Hote_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Hote_Centreon'] );
			$Consigne_Sonde = htmlspecialchars ( $res_liste_service_demande ['Consigne'] );
			
			// met à jour le modèle de service Changement
			
			// vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
			$req_type_modele = $bdd_supervision->prepare ( 'SELECT MS.MS_EST_MACRO FROM ' . $tbl_service . ' AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Service_Centreon = :ID_Service_Centreon' );
			$req_type_modele->execute ( Array (
					'ID_Service_Centreon' => $ID_Service_Centreon 
			) ) or die ( print_r ( $req_type_modele->errorinfo () ) );
			
			while ( $res_type_modele = $req_type_modele->fetch () ) // ne doit retourner qu'un seul enregistrement
			{
				if ($res_type_modele [0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
				{
// appel procédure commune de traitement des macros
					include('requete_traitement_macros.php');
					
// 					$EST_MACRO = True;
// 	// 				if ($ID_Service_Centreon == "31932"){
// 	// 					addlog("EST_MACRO=".$EST_MACRO);
// 	// 				};
// 					// récupère les arguments de type Macro
// 					// 1) récupère la liste exhaustive des macro liées à la commande avec un maximum de 7 modèles (ce qui doit être largement suffisant)
// 					// +-----------------------------------------------------------------------------------------------------------+
// 					// | Macro |
// 					// +-----------------------------------------------------------------------------------------------------------+
// 					// | $_SERVICEINTERFACEID$ -w $_SERVICEWARNING$ -c $_SERVICECRITICAL$ -T $_SERVICEIFSPEED$ -S $_SERVICE64BITS$ |
// 					// +-----------------------------------------------------------------------------------------------------------+
// 					$req_Select_Macro = $bdd_centreon->prepare ( '
// 					SELECT REPLACE(TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))),"\"","") AS Macro
// 					FROM service AS S
// 					LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// 					LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// 					LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// 					LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// 					LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// 					LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// 					LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// 					LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
// 					WHERE c.command_line IS NOT NULL
// 						AND TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))) <> ""
// 						AND S.service_id= :ID_Service_Centreon' );
// 					$req_Select_Macro->execute ( Array (
// 							'ID_Service_Centreon' => $ID_Service_Centreon 
// 					) ) or die ( print_r ( $req_Select_Macro->errorinfo () ) );
					
// 	// 				echo '<pre>';
// 	// 				print_r($req_Select_Macro);
// 	// 				echo '</pre>';
	
// 					// 2) extrait chaque Macro de la chaine
// 					$Chaine_Macro = "";
// 					while ( $res_Select_Macro = $req_Select_Macro->fetch () ) {
// 						$Chaine_Macro .= " " . htmlspecialchars ( $res_Select_Macro ['Macro'] );
// 	 					if ($ID_Service_Centreon == "15224"){
// 	 						addlog("Chaine_Macro=".$Chaine_Macro);
// 	 					};
// 					};
// 					//addlog("Chaine_Macro=".$Chaine_Macro);
// 					$T_Chaine_Macro = explode ( " ", TRIM ( $Chaine_Macro ) ); // découpe la chaine en tableau
					
// 					if ($ID_Service_Centreon == "15224"){
// 						echo '<pre>';
// 						print_r($T_Chaine_Macro);
// 	 					echo '</pre>';
// 					};
	 				
					
// 					$NbLigne = count ( $T_Chaine_Macro );
// 					$Liste_Macro = Array (); // recrée un nouveau tableau qui contiendra uniquement les noms des macro
// 					$i = 0;
// 					for($j = 0; $j < $NbLigne; $j ++) {
// 						// echo "ChaineMacro=".substr($T_Chaine_Macro[$j],0,9) . "\n";
// 						// récupération de la chaine après $_SERVICE
// 						$chaine=substr($T_Chaine_Macro[$j],strpos($T_Chaine_Macro[$j],"\$_SERVICE"));
// 						$chaine=substr($chaine,0,strpos($chaine,"\$"));
// 						//if (substr ( $T_Chaine_Macro [$j], 0, 9 ) == "\$_SERVICE") {
// 						if (substr ( $chaine, 0, 9 ) == "\$_SERVICE") {
// 							// $Liste_Macro[$i] = substr($res_liste_Macro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
// 							//$Liste_Macro [$i] = $T_Chaine_Macro [$j]; // retourne la valeur de la macro et la stocke dans un nouveau tableau
// 							$Liste_Macro[$i] = substr($res_liste_Macro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
// 	 						if ($ID_Service_Centreon == "15224"){
// 	 							//addlog("Liste_Macro=".$Liste_Macro [$i]);
// 	 							echo '<pre>';
// 	 							print_r($Liste_Macro);
// 	 							echo '</pre>';
// 	 						};
// 							$i ++;
// 						};
// 					};
					
// 	// 				echo '<pre>';
// 	// 				print_r($Liste_Macro);
// 	// 				echo '</pre>';
					
// 					// 3) récupérer la chaine des modèles afin de récupérer la liste des valeurs de chaque modèle
// 					// +------------+------------+------------+------------+------------+------------+------------+------------+
// 					// | service_id | service_id | service_id | service_id | service_id | service_id | service_id | service_id |
// 					// +------------+------------+------------+------------+------------+------------+------------+------------+
// 					// | 6405 | 7239 | 5325 | 878 | 5334 | NULL | NULL | NULL |
// 					// +------------+------------+------------+------------+------------+------------+------------+------------+
					
// 	//				$req_Liste_Modele = $bdd_centreon->prepare ( 'select DISTINCT S.service_id,T1.service_id,T2.service_id,T3.service_id,T4.service_id,T5.service_id,T6.service_id,T7.service_id
// 					$req_Liste_Modele = $bdd_centreon->prepare ( 'select DISTINCT T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id
// 						FROM service AS S
// 						LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
// 						LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
// 						LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
// 						LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
// 						LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
// 						LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
// 						LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
// 						LEFT JOIN on_demand_macro_service AS M on M.svc_svc_id=coalesce(T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id)
// 						WHERE S.Service_id = :ID_Service_Centreon' );
// 					$req_Liste_Modele->execute ( Array (
// 							'ID_Service_Centreon' => $ID_Service_Centreon 
// 					) ) or die ( print_r ( $req_Liste_Modele->errorInfo () ) );
// 					// 4) boucle sur les id pour remplir chaque macro
// 					// on charge l'ensemble des valeur de macro
// 					$Macro = False; // indicateur
// 					$NbMacro = count ( $Liste_Macro );
// 					$Val_Macro = Array ();
// 					while ( $res_Liste_Modele = $req_Liste_Modele->fetch () ) // pour chaque service_id trouvé
// 					{ // on recherche les valeurs de macro renseignée avec une boucle sur les 8 service_id
// 						for($k = 0; $k < 8; $k ++)
// 						{
// 							$svc_svc_id = htmlspecialchars ( $res_Liste_Modele [$k] );
// 	// 						if ($ID_Service_Centreon == "31932"){
// 	// 							addlog("svc_svc_id=".$svc_svc_id);
// 	// 						};
							
// 							if (($svc_svc_id != NULL) or ($svc_svc_id != "")) // si le modèle n'est pas null, on traite
// 							{
// 								$req_Macro_Valeur = $bdd_centreon->prepare ('SELECT svc_macro_name,svc_macro_value FROM on_demand_macro_service WHERE svc_svc_id= :svc_svc_id');
// 								$req_Macro_Valeur->execute ( Array (
// 										'svc_svc_id' => $svc_svc_id 
// 								) ) or die ( print_r ( $req_Macro_Valeur->errorInfo () ) );
// 								$res_Macro_Valeur = $req_Macro_Valeur->fetchall ();
// 								/*
// 								 * echo '<pre>';
// 								 * print_r($res_Macro_Valeur);
// 								 * echo '</pre>';
// 								 */
// 								for($j = 0; $j < $NbMacro; $j ++) // pour chaque Liste_Macro
// 								{
// 									// foreach ($res_Macro_Valeur AS $Macro_Name => $Macro_Valeur) // on boucle sur les valeurs remontée par la requête
// 									foreach ( $res_Macro_Valeur as $Macro_Name ) // on boucle sur les valeurs remontée par la requête
// 									{
// 	// 									if ($ID_Service_Centreon == "31932"){
// 	// 										addlog("Liste_MacroJ=".$Liste_Macro[$j]);
// 	// 										addlog("Macro_Name=".$Macro_Name[0]);
// 	// 										addlog("Macro_Valeur=".$Macro_Name[1]);
// 	// 									};
// 										// if (($Liste_Macro[$j] == $Macro_Name[0]) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 										if ((strcasecmp ( $Liste_Macro [$j], $Macro_Name [0] ) == 0) and ($Macro_Name [1] != "")) // Si Liste_Macro = Macro_Name et Macro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
// 										{// strcasecmp => comparaison insensible à la casse
// 											$Val_Macro [$Macro_Name [0]] = substr ( $Macro_Name [0], 9, - 1 ) . ":" . $Macro_Name [1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
// 	// 										if ($ID_Service_Centreon == "31932"){
// 	// 											addlog("Liste_Macro-J=".$Liste_Macro[$j]);
// 	// 											addlog("Macro_Name=".$Macro_Name[0]);
// 	// 											addlog("Val_Macro=".$Val_Macro[$Macro_Name [0]]);
// 	// 										};
// 											// exemple IFSPEED:1000
// 										};
// 									};
// 								};
// 							};
// 						};
// 					};
					
// 	// 				if ($ID_Service_Centreon == "31932"){
					
// 	// 					echo '<pre>';
// 	// 					print_r($res_Liste_Modele);
// 	// 					echo '</pre>';
// 	// 					echo '<pre>';
// 	// 					print_r($Val_Macro);
// 	// 					echo '</pre>';
// 	// 				};
					
					// 7) On construit la chaine des Macro selon le modèle des arguments
					$Chaine_Val_Macro = "";
					foreach ( $Val_Macro as $Macro_Nom => $Macro_Val )
					{
						$Chaine_Val_Macro .= "!" . $Macro_Val;
	// 					if ($ID_Service_Centreon == "31932"){
	// 						addlog("Chaine_Val_Macro=".$Chaine_Val_Macro);
	// 					};
					};
					$Chaine_Val_Macro = substr($Chaine_Val_Macro,1); // stocke les arguments sans le premier !
					$Liste_Argument = $Chaine_Val_Macro . "#" . $Consigne_Sonde;
					$req_C_service = explode ( "#", $Liste_Argument ); // conversion de la chaine en tableau
					                                               
					// on insère les arguments en base
	
	// 				echo '<pre>';
	// 				print_r($req_C_service);
	// 				echo '</pre>';
					
					$NbLigne = count ( $req_C_service );
					addlog("argument_service_id".$ID_Service_Centreon."=".substr ( htmlspecialchars ( $req_C_service [0] ), 1 ));
					// $upd_service = $bdd_supervision->prepare('UPDATE ' . $tbl_service . ' SET Parametres= :Parametres, Consigne= :Consigne WHERE ID_Service_Centreon = :ID_Service_Centreon');
					$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_service . ' SET Parametres= :Parametres WHERE ID_Service_Centreon = :ID_Service_Centreon' );
					$upd_service->execute ( Array (
							'Parametres' => htmlspecialchars(substr($req_C_service[0],1)),
							'ID_Service_Centreon' => $ID_Service_Centreon 
					) ) or die ( print_r ( $upd_service->errorInfo () ) );
				};
			};
	// 		$num_service++;
		};
		// addlog("boucle sur chaque service de la table tmp_service OK.");
		
		// Selection des éléments hôte
		// addlog("selection hote pour creation tableau php...");
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
		// addlog("selection hote pour creation tableau php... OK.");
		
		// Sélection des éléments service
		// addlog("selection service pour creation tableau php...");
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
		//include ('requete_Remplissage_Service.php');
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
		//addlog("Nbre_Service=" . $nb_service);
		
		// Sélection des éléments periode
		// addlog("selection periode pour creation tableau php...");
		$SEL_tmp_periode = $bdd_supervision->prepare ( 'SELECT Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM ' . $tbl_periode . '' );
		$SEL_tmp_periode->execute ( Array () ) or die ( print_r ( $SEL_tmp_periode->errorInfo () ) );
		
		$r_plage = $SEL_tmp_periode->fetchAll ();
		
		$nb_plage = count ( $r_plage );
		// addlog("selection periode pour creation tableau php... OK.");
		$bdd_supervision->commit();
	} else
	{
		addlog("KO - aucun élément trouvé pour la prestation[" . $prestation . "].");
		echo "<p>Aucun élément n'a été trouvé pour cette prestation.</p>";
		$bdd_supervision->commit();
		//return true;
		exit;
	};
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur requete_extraction_elements: ' . $e->getMessage());
};
