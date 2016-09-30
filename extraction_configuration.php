<?php
if (session_id () == '') {
	session_start ();
};
//include ('log.php'); // chargement de la fonction de log
include_once ('connexion_sql_centreon.php');
include_once ('connexion_sql_supervision.php');

echo time();
	/**
	 * découpage de l'extraction de la configuration
	 * 1 Selection des hôtes et services de centreon hors modèles
	 * 2 Insertion des éléments en base
	 * 3 Consolidation des categories d'hôtes
	 * 4 Consolidation des groupes d'hôtes
	 * 5 Consolidation des categories de service
	 */
	
	/**
	 * 1 Selection des hôtes et services de centreon
	 */
//	addlog("selection hote pour la prestation " . $prestation . "...");
	$truncate = $bdd_supervision->query('TRUNCATE TABLE extraction_configuration;') or die(print_r($truncate->errorInfo()));

	$req_extract = $bdd_centreon->prepare ( 'SELECT
			h.host_name,
			s.service_description,
			h.host_id,
			h.host_address,
			s.service_id
			 FROM host h
			 INNER JOIN host_service_relation hsr ON h.host_id=hsr.host_host_id
			 INNER JOIN service s ON s.service_id=hsr.service_service_id
			 WHERE h.host_name NOT LIKE "_Module%" AND h.host_register="1"
			 GROUP BY 1,2
			 ORDER BY 1,2;');
	$req_extract->execute(array()) or die(print_r($req_extract->errorInfo()));
	//addlog("extraction OK...");
	$res_extract = $req_extract->fetchAll ();
	/**
	 * 2 Insertion des éléments en base
	 */
	$i=0;
	$values="";
	foreach ( $res_extract as $elements )
	{
// 		$Localisation = stristr ( $res_elements ['Nom_Hote'], '-', TRUE ); // récupère la localisation => les caractères avant le premier tiret
// 		$Type = stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-', TRUE ); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
// 		$Nom_Hote = substr ( stristr ( substr ( stristr ( $res_elements ['Nom_Hote'], '-' ), 1 ), '-' ), 1 ); // enlève localisation et type
		
		
		
// 		$values .= ",(" . $elements['host_id'] . ",'" . $elements['host_name'] . "','" . $elements['host_address'] . "'," . $elements['service_id'] . ",'" . $elements['service_description'] . "'," . $elements['service_template_id'] . ",'" . $elements['service_template_name'] . "','" . $Cat_All . "','" . $Cat_Archi . "','" . $Cat_Fonction . "','" . $Cat_Langue . "','" . $Cat_OS . "','" . $Cat_Type . "','" . $Grp_All . "','" . $Grp_Type . "','" . $Grp_Solution . "','" . $Grp_Site . "','" . $categorie . "','" . $souscategorie . "')";
		$values .= ",(" . $elements['host_id'] . ",'" . $elements['host_name'] . "','" . $elements['host_address'] . "'," . $elements['service_id'] . ",'" . $elements['service_description'] . "')";
		if ($i % 100 == 0)
		{
			//addlog("insertion partielle " . $i/100 . "...");
			$values = substr($values,1); // suppression de la première virgule
			//addlog($values);
			$insertion = $bdd_supervision->prepare(
//					'INSERT INTO extraction_configuration (ec_host_id, ec_host_name, ec_host_address, ec_service_id, ec_service_description, ec_service_template_id, ec_service_template_name, ec_host_categorie_all, ec_host_categorie_architecture, ec_host_categorie_fonction, ec_host_categorie_langue, ec_host_categorie_os, ec_host_categorie_type, ec_host_group_all, ec_host_group_type, ec_host_group_solution, ec_host_group_site, ec_service_categorie_type, ec_service_categorie_soustype ) VALUES ' . $values . '');
					'INSERT INTO extraction_configuration (ec_host_id, ec_host_name, ec_host_address, ec_service_id, ec_service_description) VALUES ' . $values . '');
			$insertion->execute(array()) or die(print_r($insertion->errorInfo()));
			$values = "";
		};
		$i++;
	};
	//addlog("insertion finale");
	$values = substr($values,1); // suppression de la première virgule
	//addlog($values);
	$insertion = $bdd_supervision->prepare(
//			'INSERT INTO extraction_configuration (ec_host_id, ec_host_name, ec_host_address, ec_service_id, ec_service_description, ec_service_template_id, ec_service_template_name, ec_host_categorie_all, ec_host_categorie_architecture, ec_host_categorie_fonction, ec_host_categorie_langue, ec_host_categorie_os, ec_host_categorie_type, ec_host_group_all, ec_host_group_type, ec_host_group_solution, ec_host_group_site, ec_service_categorie_type, ec_service_categorie_soustype ) VALUES ' . $values . '');
			'INSERT INTO extraction_configuration (ec_host_id, ec_host_name, ec_host_address, ec_service_id, ec_service_description) VALUES ' . $values . '');
			$insertion->execute(array()) or die(print_r($insertion->errorInfo()));
	$values = "";
	

	/**
	 * Traitement des groupes d'hôte
	 * explode la chaine hg_name
	 */
	$req_extract_groupe = $bdd_centreon->prepare(
			'SELECT DISTINCT h.host_id AS host_id,
				 GROUP_CONCAT(DISTINCT hg.hg_name) AS hg_name
			 FROM host h
			 INNER JOIN hostgroup_relation hgr ON hgr.host_host_id=h.host_id
			 INNER JOIN hostgroup hg ON hg.hg_id=hgr.hostgroup_hg_id
			 WHERE h.host_name NOT LIKE "_Module%" AND h.host_register="1"
			 GROUP BY 1;');
	$req_extract_groupe->execute(Array()) or die(print_r($req_extract_groupe->errorInfo()));
	$res_extract_groupe = $req_extract_groupe->fetchAll ();


	foreach ($res_extract_groupe as $elements)
	{
// 		echo "hg_name pour " . $elements ['host_id']."=".$elements ['hg_name']."\n";
		
		$hostgroup=explode(",",htmlspecialchars ( $elements ['hg_name'] ));
		$Nb = count($hostgroup);
		$Grp_All="";
		$Grp_Type="";
		$Grp_Site="";
		$Grp_Solution="";
		$Grp_inconnu="";
		for ( $j=0;$j<$Nb;$j++ )
		{
			If ($hostgroup [$j] != "All")
			{
				$group = stristr($hostgroup [$j],'_',TRUE); // récupère la chaine AVANT le _
			} Else
			{
				$group = "All";
			};

			//			addlog("categorie=" . $categorie);


			$valeur= substr(stristr($hostgroup [$j],'_',FALSE ),1); // récupère la chaine APRES le _
// echo "Group=" . $group . "\n";
// echo "Valeur=" . $valeur . "\n";

			if ($group == "Type") // si hostgroup est "Type"
			{
				$Grp_Type = $valeur;
			} else if ($group == "Solution") // si hostgroup est "Solution"
			{
				$Grp_Solution = $valeur;
			} else if ($group == "Site") // si hostgroup est "Site"
			{
				$Grp_Site = $valeur;
			} else if ($group == "All") // si hostgroup est "All"
			{
				$Grp_All = $group;
// 			} else if ($group != "") // si categorie n'est pas vide
// 			{
// 				$Grp_inconnu = $group;
// 				echo "Erreur: Group [" . $Grp_inconnu . "] non prise en charge";
// 				//addlog ( "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge" );
// 				Return False;
			};
		};
// 		echo "Grp_Type=".$Grp_Type."\n";
// 		echo "Grp_Site=".$Grp_Site."\n";
// 		echo "Grp_Solution=".$Grp_Solution."\n";
// 		echo "Grp_All=".$Grp_All."\n";
		
		$insertion = $bdd_supervision->prepare(
				'UPDATE extraction_configuration
				 SET ec_host_group_all= :ec_host_group_all,
					 ec_host_group_type= :ec_host_group_type,
					 ec_host_group_site= :ec_host_group_site,
					 ec_host_group_solution= :ec_host_group_solution
				 WHERE ec_host_id= :host_id');
		$insertion->execute(array(
				'ec_host_group_all' => htmlspecialchars($Grp_All),
				'ec_host_group_type' => htmlspecialchars($Grp_Type),
				'ec_host_group_site' => htmlspecialchars($Grp_Site),
				'ec_host_group_solution' => htmlspecialchars($Grp_Solution),
				'host_id' => htmlspecialchars($elements['host_id'])
		)) or die(print_r($insertion->errorInfo()));
	};

	/**
	 * Traitement des catégories
	 * explode la chaine hc_name
	 */
	
	$req_extract_categorie = $bdd_centreon->prepare(
		'SELECT DISTINCT h.host_id AS host_id,
			 GROUP_CONCAT(DISTINCT hc.hc_name) AS hc_name
			 FROM host h
			 INNER JOIN hostcategories_relation hcr ON h.host_id=hcr.host_host_id
			 INNER JOIN hostcategories hc ON hc.hc_id=hcr.hostcategories_hc_id
			WHERE h.host_name NOT LIKE "_Module%" AND h.host_register="1"
			GROUP BY 1;');
	$req_extract_categorie->execute(Array()) or die(print_r($req_extract_categorie->errorInfo()));
	$res_extract_categorie = $req_extract_categorie->fetchAll ();
	
	foreach ($res_extract_categorie as $elements)
	{
		$chaine=explode(",",htmlspecialchars ( $elements ['hc_name'] ));
		$Nb = count($chaine);
			$Cat_All="";
			$Cat_Archi="";
			$Cat_Fonction="";
			$Cat_Langue="";
			$Cat_OS="";
			$Cat_Type="";

		for ( $j=0;$j<$Nb;$j++ )
		{
			If ($chaine [$j] != "All")
			{
				$categorie = stristr($chaine [$j],'_',TRUE); // récupère la chaine AVANT le _
			} Else
			{
				$categorie = "All";
			};
	
	//			addlog("categorie=" . $categorie);
	
			$valeur= substr(stristr($chaine [$j],'_',FALSE ),1); // récupère la chaine APRES le _
			if ($categorie == "Architecture") // si categorie est "Archi"
			{
				$Cat_Archi = $valeur;
			} else if ($categorie == "OS") // si categorie est "OS"
			{
				$Cat_OS = $valeur;
			} else if ($categorie == "Langue") // si categorie est "Langue"
			{
				$Cat_Langue = $valeur;
			} else if ($categorie == "Type") // si categorie est "Type"
			{
				$Cat_Type = $valeur;
			} else if ($categorie == "Fonction") // si categorie est "Fonction"
			{
				$Cat_Fonction .= $valeur . ",";
			} else if ($categorie == "All") // si categorie est "Fonction"
			{
				$Cat_All = $categorie;
			} else if ($categorie != "") // si categorie n'est pas vide
			{
				$Cat_Inconnue .= $categorie . ",";
				echo "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge";
				//addlog ( "Erreur: Categorie [" . $Cat_Inconnue . "] non prise en charge" );
				Return False;
			};
		};
		$insertion = $bdd_supervision->prepare(
				'UPDATE extraction_configuration
				 SET ec_host_categorie_all= :ec_host_categorie_all,
					 ec_host_categorie_architecture= :ec_host_categorie_architecture,
					 ec_host_categorie_fonction= :ec_host_categorie_fonction,
					 ec_host_categorie_langue= :ec_host_categorie_langue,
					 ec_host_categorie_os= :ec_host_categorie_os,
					 ec_host_categorie_type= :ec_host_categorie_type
				 WHERE ec_host_id= :host_id');
		$insertion->execute(array(
				'ec_host_categorie_all' => htmlspecialchars($Cat_All),
				'ec_host_categorie_architecture' => htmlspecialchars($Cat_Archi),
				'ec_host_categorie_fonction' => htmlspecialchars($Cat_Fonction),
				'ec_host_categorie_langue' => htmlspecialchars($Cat_Langue),
				'ec_host_categorie_os' => htmlspecialchars($Cat_OS),
				'ec_host_categorie_type' => htmlspecialchars($Cat_Type),
				'host_id' => htmlspecialchars($elements['host_id'])
		)) or die(print_r($insertion->errorInfo()));
	};
	
	/**
	 * Fonction traitement des modèles d'hôtes
	 * extraction puis insertion
	 */
	$req_extract_modele = $bdd_centreon->prepare(
			'SELECT DISTINCT h.host_id AS host_id,
			 s.service_id AS service_id,
			 ht.host_name AS host_template_name,
			 ht.host_id AS host_template_id
			 FROM host h
			 INNER JOIN host_service_relation hsr ON h.host_id=hsr.host_host_id
			 INNER JOIN service s ON s.service_id=hsr.service_service_id
			 INNER JOIN service st ON st.service_id=s.service_template_model_stm_id
			 INNER JOIN host_service_relation hsrt ON hsrt.service_service_id=st.service_id
			 INNER JOIN host_template_relation htr ON hsrt.host_host_id=htr.host_tpl_id AND h.host_id=htr.host_host_id
			 INNER JOIN host ht ON ht.host_id=htr.host_tpl_id
			 WHERE h.host_name NOT LIKE "_Module%" AND h.host_register="1"
			 GROUP BY 1,2;');
	$req_extract_modele->execute(Array()) or die(print_r($req_extract_modele->errorInfo()));
	$res_extract_modele = $req_extract_modele->fetchAll ();
	
	foreach ( $res_extract_modele as $elements )
	{
		$insertion = $bdd_supervision->prepare(
				'UPDATE extraction_configuration
				 SET ec_host_template_id= :host_template_id, ec_host_template_name= :host_template_name
				 WHERE ec_host_id= :host_id AND ec_service_id= :service_id');
		$insertion->execute(array(
				'host_template_id' => htmlspecialchars($elements['host_template_id']),
				'host_template_name' => htmlspecialchars($elements['host_template_name']),
				'host_id' => htmlspecialchars($elements['host_id']),
				'service_id' => htmlspecialchars($elements['service_id'])
		)) or die(print_r($insertion->errorInfo()));
	};

	/**
	 * Traitement des modeles et catégories service
	 * explode la chaine sc_description
	 */

	
	$req_extract_sc = $bdd_centreon->prepare(
		'SELECT DISTINCT h.host_id AS host_id,
			 s.service_id AS service_id,
			 st.service_id AS st_id,
			 st.service_description AS st_description,
			 GROUP_CONCAT(DISTINCT sc.sc_description) as sc_description
		FROM host h
			 INNER JOIN host_service_relation hsr ON h.host_id=hsr.host_host_id
			 INNER JOIN service s ON s.service_id=hsr.service_service_id
			 INNER JOIN service st ON st.service_id=s.service_template_model_stm_id
			 INNER JOIN service_categories_relation scr ON st.service_id=scr.service_service_id
			 LEFT JOIN service_categories sc ON sc.sc_id=scr.sc_id AND sc.level IS NULL AND sc.level IS NULL
		 WHERE h.host_name NOT LIKE "_Module%" AND h.host_register="1"
		 GROUP BY 1,2;');
	$req_extract_sc->execute(Array()) or die(print_r($req_extract_sc->errorInfo()));
	
	$res_extract_sc = $req_extract_sc->fetchAll ();
	foreach ( $res_extract_sc as $elements )
	{
// echo "sc_description=" . $elements ['sc_description'] ."\n";
		$st_id=htmlspecialchars ( $elements ['st_id']);
		$st_description=htmlspecialchars ( $elements ['st_description']);
		$categories=explode(",",htmlspecialchars ( $elements ['sc_description'] ));
		$categories=explode(",",htmlspecialchars ( $elements ['sc_description'] ));
		$Nb = count($categories);
		$categorie="";
		$souscategorie="";
		for ( $j=0;$j<$Nb;$j++ )
		{
// echo "list_categorie=" . $categories [$j] . "\n";
			If (substr($categories [$j],0,4) == "Type") // Si la categorie contient Type
			{
				$categorie = substr(stristr($categories [$j],'_',FALSE),1); // récupère la chaine APRES le _
			} Else
			{
				$souscategorie = $categories [$j];
			};
		};
// echo "categorie=" . $categorie . "\n";
// echo "souscategorie=" . $souscategorie . "\n";
		
		$insertion = $bdd_supervision->prepare(
				'UPDATE extraction_configuration
				 SET ec_service_template_id= :ec_service_template_id,
					 ec_service_template_name= :ec_service_template_name,
					 ec_service_categorie_type= :ec_service_categorie_type,
					 ec_service_categorie_soustype= :ec_service_categorie_soustype
				 WHERE ec_host_id= :host_id AND ec_service_id= :service_id');
		$insertion->execute(array(
				'ec_service_template_id' => htmlspecialchars($st_id),
				'ec_service_template_name' => htmlspecialchars($st_description),
				'ec_service_categorie_type' => htmlspecialchars($categorie),
				'ec_service_categorie_soustype' => htmlspecialchars($souscategorie),
				'host_id' => htmlspecialchars($elements['host_id']),
				'service_id' => htmlspecialchars($elements['service_id'])
		)) or die(print_r($insertion->errorInfo()));
	};
	
	
	/**
	 * Extraction des données consolidées pour insertion dans le ficheir csv
	 * Champ inérés dans le fichier:
	 * 	Hote, Grp_Type, Grp_Site, Grp_Solution, Modele_Hote, Cat_Hote_All, Cat_Hote_Architecture, Cat_Hote_Fonction, Cat_Hote_Langue, Cat_Hote_Os, Cat_Hote_Type, Service, Modele_Service, Categorie_Service, SousCategorie_Service
	 */
	$req_select = $bdd_supervision->prepare("
			SELECT
			 CONCAT(IFNULL(ec_host_name,''),';',IFNULL(ec_host_group_type,''),';',IFNULL(ec_host_group_site,''),';',
			IFNULL(ec_host_group_solution,''),';',IFNULL(ec_host_template_name,''),';',
			IFNULL(ec_host_categorie_all,''),';',IFNULL(ec_host_categorie_architecture,''),';',
			IFNULL(ec_host_categorie_fonction,''),';',IFNULL(ec_host_categorie_langue,''),';',
			IFNULL(ec_host_categorie_os,''),';',IFNULL(ec_host_categorie_type,''),';',
			IFNULL(ec_service_description,''),';',IFNULL(ec_service_template_name,''),';',
			IFNULL(ec_service_categorie_type,''),';',IFNULL(ec_service_categorie_soustype,''))
			 FROM extraction_configuration
			 ORDER BY ec_host_name,ec_service_description;
			");
	$req_select->execute(array()) or die(print_r($req_select->errorInfo()));
	
	$file = fopen("extraction_pdf/extraction_configuration.csv", "w");
	fwrite($file, "Hote;Grp_Type;Grp_Site;Grp_Solution;Modele_Hote;Cat_Hote_All;Cat_Hote_Architecture;Cat_Hote_Fonction;Cat_Hote_Langue;Cat_Hote_Os;Cat_Hote_Type;Service;Modele_Service;Cat_Service;SousCat_Service\n");
	$values = "";
	while ( $res_select = $req_select->fetch () )
	{
		$values .= $res_select[0] . "\n";
	};
	fwrite($file, $values);
	fclose($file);
	echo '<a href="./extraction_pdf/extraction_configuration.csv" target="_blank">Cliquez ici pour télécharger le fichier csv</a> </br>';
	echo "Traitement terminé.\n";

	echo time();