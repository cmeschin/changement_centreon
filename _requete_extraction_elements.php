<?php
if (session_id () == '') {
	session_start ();
}
;
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include ('log.php'); // chargement de la fonction de log
$prestation = (isset ( $_POST ["prestation"] )) ? $_POST ["prestation"] : NULL;

include_once ('connexion_sql_centreon.php');

$req_elements = $bdd_centreon->prepare ( 'SELECT 
		 Distinct(Nom_Hote) as Nom_Hote,
		 Hote_Description as Description,
		 IP_Hote as IP_Hote,
		 Controle as Controle_Actif,
		 Controle_Hote as Controle_Hote_Actif,
		 Sonde as Nom_Service,
		 Argument as Parametres,
		 Consigne_Sonde as Consigne_Service,
		 Frequence,
		 Plage_Horaire as Nom_Periode,
		 Lundi,
		 Mardi,
		 Mercredi,
		 Jeudi,
		 Vendredi,
		 Samedi,
		 Dimanche,
		 host_id as ID_Hote_Centreon,
		 service_id as ID_Service_Centreon,
		 service_modele_id as ID_Modele_Service_Centreon
		FROM vInventaireServices
		WHERE Code_Client= :prestation
		ORDER BY Nom_Hote,Sonde' );
$req_elements->execute ( array (
		'prestation' => htmlspecialchars ( $prestation ) 
) ) or die ( print_r ( $req_elements->errorInfo () ) );

$tbl_tmp_hote = "tmp_hote_" . $_SESSION ['ref_tmp_extract'];
$tbl_tmp_service = "tmp_service_" . $_SESSION ['ref_tmp_extract'];
$tbl_tmp_periode = "tmp_periode_" . $_SESSION ['ref_tmp_extract'];

include_once ('connexion_sql_supervision.php');
// suppression des tables temporaires si elles existent
$DROP_tmp_hote = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_tmp_hote . '' );
$DROP_tmp_service = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_tmp_service . '' );
$DROP_tmp_periode = $bdd_supervision->query ( 'DROP TABLE IF EXISTS ' . $tbl_tmp_periode . '' );

// création des tables temporaires pour formalisation
$CRE_tmp_hote = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_tmp_hote . ' (
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

$CRE_tmp_service = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_tmp_service . ' (
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

$CRE_tmp_periode = $bdd_supervision->query ( 'CREATE TEMPORARY TABLE ' . $tbl_tmp_periode . ' (
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
while ( $res_elements = $req_elements->fetch () ) {
	$Localisation = stristr ( htmlspecialchars ( $res_elements ['Nom_Hote'] ), '-', TRUE ); // récupère la localisation => les caractères avant le premier tiret
	$Type = stristr ( substr ( stristr ( htmlspecialchars ( $res_elements ['Nom_Hote'] ), '-' ), 1 ), '-', TRUE ); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
	$Nom_Hote = substr ( stristr ( substr ( stristr ( htmlspecialchars ( $res_elements ['Nom_Hote'] ), '-' ), 1 ), '-' ), 1 ); // enlève localisation et type
	
	$insert_hote_liste = $bdd_supervision->prepare ( 'INSERT INTO ' . $tbl_tmp_hote . ' (Nom_Hote, ID_Hote_Centreon, Description, IP_Hote, Controle_Actif, Type_Hote, ID_Localisation)
			 VALUES(:nom_hote, :id_hote_centreon, :description, :ip_hote, :controle_actif, :type_hote, :id_localisation)
			 ON DUPLICATE KEY UPDATE Nom_Hote= :nom_hote2, ID_Hote_Centreon= :id_hote_centreon2, Description= :description2, IP_Hote= :ip_hote2, Controle_Actif= :controle_actif2, Type_Hote= :type_hote2, ID_Localisation= :id_localisation2' );
	$insert_hote_liste->execute ( array (
			'nom_hote' => $Nom_Hote,
			'id_hote_centreon' => htmlspecialchars ( $res_elements ['ID_Hote_Centreon'] ),
			'description' => htmlspecialchars ( $res_elements ['Description'] ),
			'ip_hote' => htmlspecialchars ( $res_elements ['IP_Hote'] ),
			'controle_actif' => htmlspecialchars ( $res_elements ['Controle_Hote_Actif'] ),
			'type_hote' => $Type,
			'id_localisation' => $Localisation,
			'nom_hote2' => $Nom_Hote,
			'id_hote_centreon2' => htmlspecialchars ( $res_elements ['ID_Hote_Centreon'] ),
			'description2' => htmlspecialchars ( $res_elements ['Description'] ),
			'ip_hote2' => htmlspecialchars ( $res_elements ['IP_Hote'] ),
			'controle_actif2' => htmlspecialchars ( $res_elements ['Controle_Hote_Actif'] ),
			'type_hote2' => $Type,
			'id_localisation2' => $Localisation 
	) ) or die ( print_r ( $insert_hote_liste->errorInfo () ) );
	// addlog("insertion hôte: [" . $res_elements['Nom_Hote'] . "] [" . $res_elements['ID_Hote_Centreon'] . "] [" . $res_elements['Description'] . "] [" . $res_elements['IP_Hote'] . "] dans table " .$tbl_tmp_hote . "");

	$insert_service_selec = $bdd_supervision->prepare ( 'INSERT INTO ' . $tbl_tmp_service . ' (Nom_Service, Frequence, Nom_Periode, Controle_Actif, ID_Service_Centreon, ID_Hote_Centreon, ID_Modele_Service_Centreon, Consigne, Nom_Hote, Parametres)
			 VALUES(:Nom_Service, :Frequence, :Nom_Periode, :Controle_Actif, :ID_Service_Centreon, :ID_Hote_Centreon, :ID_Modele_Service_Centreon, :consigne, :nom_hote, :parametres)
			 ON DUPLICATE KEY UPDATE Nom_Service= :nom_service2, Frequence= :frequence2, Nom_Periode= :nom_periode2, Controle_Actif= :controle_actif2, ID_Service_Centreon= :id_service_centreon2, ID_Hote_Centreon= :id_hote_centreon2, ID_Modele_Service_Centreon= :id_modele_service_centreon2, Consigne= :consigne2, Nom_Hote= :nom_hote2, Parametres= :parametres2' );
	$insert_service_selec->execute ( array (
			'Nom_Service' => htmlspecialchars ( $res_elements ['Nom_Service'] ),
			'Frequence' => htmlspecialchars ( $res_elements ['Frequence'] ),
			'Nom_Periode' => htmlspecialchars ( $res_elements ['Nom_Periode'] ),
			'Controle_Actif' => htmlspecialchars ( $res_elements ['Controle_Actif'] ),
			'ID_Service_Centreon' => htmlspecialchars ( $res_elements ['ID_Service_Centreon'] ),
			'ID_Hote_Centreon' => htmlspecialchars ( $res_elements ['ID_Hote_Centreon'] ),
			'ID_Modele_Service_Centreon' => htmlspecialchars ( $res_elements ['ID_Modele_Service_Centreon'] ),
			'consigne' => htmlspecialchars ( $res_elements ['Consigne_Service'] ),
			'nom_hote' => $Nom_Hote,
			'parametres' => substr ( htmlspecialchars ( $res_elements ['Parametres'] ), 1 ), // on stocke les arguments sans le premier caractère
			'nom_service2' => htmlspecialchars ( $res_elements ['Nom_Service'] ),
			'frequence2' => htmlspecialchars ( $res_elements ['Frequence'] ),
			'nom_periode2' => htmlspecialchars ( $res_elements ['Nom_Periode'] ),
			'controle_actif2' => htmlspecialchars ( $res_elements ['Controle_Actif'] ),
			'id_service_centreon2' => htmlspecialchars ( $res_elements ['ID_Service_Centreon'] ),
			'id_hote_centreon2' => htmlspecialchars ( $res_elements ['ID_Hote_Centreon'] ),
			'id_modele_service_centreon2' => htmlspecialchars ( $res_elements ['ID_Modele_Service_Centreon'] ),
			'consigne2' => htmlspecialchars ( $res_elements ['Consigne_Service'] ),
			'nom_hote2' => $Nom_Hote,
			'parametres2' => substr ( htmlspecialchars ( $res_elements ['Parametres'] ), 1 ) 
	) // on stocke les arguments sans le premier caractère
 ) or die ( print_r ( $insert_service_selec->errorInfo () ) );
//	 addlog("insertion service: [" . $res_elements['Nom_Service'] . "] dans table " .$tbl_tmp_service . "");
	 
	$insert_periode_liste = $bdd_supervision->prepare ( 'INSERT IGNORE INTO ' . $tbl_tmp_periode . ' (Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche)
			 VALUES(:Nom_Periode, :Lundi, :Mardi, :Mercredi, :Jeudi, :Vendredi, :Samedi, :Dimanche)' );
	$insert_periode_liste->execute ( array (
			'Nom_Periode' => htmlspecialchars ( $res_elements ['Nom_Periode'] ),
			'Lundi' => htmlspecialchars ( $res_elements ['lundi'] ),
			'Mardi' => htmlspecialchars ( $res_elements ['mardi'] ),
			'Mercredi' => htmlspecialchars ( $res_elements ['mercredi'] ),
			'Jeudi' => htmlspecialchars ( $res_elements ['jeudi'] ),
			'Vendredi' => htmlspecialchars ( $res_elements ['vendredi'] ),
			'Samedi' => htmlspecialchars ( $res_elements ['samedi'] ),
			'Dimanche' => htmlspecialchars ( $res_elements ['dimanche'] ) 
	) ) or die ( print_r ( $insert_periode_liste->errorInfo () ) );
	// addlog("insertion periode: [" . $res_elements['Nom_Periode'] . "] dans table " .$tbl_tmp_periode . "");
}
;

// ///////////////////////////////////////////////////////////////////////////////////////
// traitement des données
// ///////////////////////////////////////////////////////////////////////////////////////

$liste_hote_extract = $bdd_supervision->prepare ( 'SELECT ID_Hote_Centreon FROM ' . $tbl_tmp_hote . '' );
$liste_hote_extract->execute ( Array () ) or die ( print_r ( $liste_hote_extract->errorInfo () ) );

// on boucle sur la liste des hôte pour MAJ table
// $i = 1;
while ( $res_liste_hote_extract = $liste_hote_extract->fetch () ) {
	// addlog("passe n°". $i);
	$ID_Hote_Centreon = htmlspecialchars ( $res_liste_hote_extract ['ID_Hote_Centreon'] );
	// addlog("Selection categories");
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
	$Cat_Localisation = "";
	
	while ( $res_select_categ_hote = $select_categ_hote->fetch () ) {
		/*
		 * le 25/11/14 inutile puisque calculé plus haut
		 */
		  if ($Type == "")
		  {
		  $Type = stristr(substr(stristr(htmlspecialchars($res_select_categ_hote['host_name']),'-'),1),'-',TRUE); // enlève localisation et le tiret et récupère la fonction => les caractères entre les deux premiers tirets
		  addlog($Type);
		  };
		  $Cat_Localisation = stristr(htmlspecialchars($res_select_categ_hote['host_name']),'-',TRUE); // récupère la localisation => les caractères avant le premier tiret
		  addlog($Cat_Localisation);
		  
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
	// modif le 03-11-2014
	// $MAJ_Hote = $bdd_supervision->prepare('UPDATE ' . $tbl_tmp_hote . ' SET Type_Hote= :Type_Hote, ID_Localisation= :ID_Localisation, Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue WHERE ID_Hote_Centreon = :ID_Hote_Centreon');
	$MAJ_Hote = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_tmp_hote . ' SET Fonction= :Cat_Fonction, Architecture= :Cat_Archi, OS= :Cat_OS, Langue= :Cat_Langue, ID_Localisation= :Cat_Localisation WHERE ID_Hote_Centreon = :ID_Hote_Centreon' );
	$MAJ_Hote->execute ( Array (
			
			// 'Type_Hote' => $Type,
			'Cat_Localisation' => $Cat_Localisation,
			'Cat_Fonction' => $Cat_Fonction,
			'Cat_Archi' => $Cat_Archi,
			'Cat_OS' => $Cat_OS,
			'Cat_Langue' => $Cat_Langue,
			'ID_Hote_Centreon' => $ID_Hote_Centreon 
	) ) or die ( print_r ( $MAJ_Hote->errorInfo () ) );
	// addlog("champs table hote mis à jour!");
	// echo "champs table hote mis à jour.";
};

// ////////////////////////////////////////////////////////////////////////////////////////////////////////
// liste des services de la demande
// $liste_service_demande = $bdd_supervision->prepare('SELECT ID_Service_Centreon, ID_Hote_Centreon, Nom_Hote FROM ' . $tbl_tmp_service . '');
$liste_service_demande = $bdd_supervision->prepare ( 'SELECT ID_Service_Centreon, ID_Hote_Centreon, Consigne FROM ' . $tbl_tmp_service . '' );
$liste_service_demande->execute ( Array () ) or die ( print_r ( $liste_service_demande->errorInfo () ) );

// on boucle sur la liste des services pour MAJ table
while ( $res_liste_service_demande = $liste_service_demande->fetch () ) {
	$ID_Service_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Service_Centreon'] );
	$ID_Hote_Centreon = htmlspecialchars ( $res_liste_service_demande ['ID_Hote_Centreon'] );
	$Consigne_Sonde = htmlspecialchars ( $res_liste_service_demande ['Consigne'] );
	
	// met à jour le modèle de service Changement
	$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_tmp_service . ' AS S INNER JOIN relation_modeles AS RM ON S.ID_Modele_Service_Centreon=RM.ID_Modele_Service_Centreon SET S.ID_Modele_Service=RM.ID_Modele_Service' );
	$upd_service->execute ( Array () ) or die ( print_r ( $upd_service->errorInfo () ) );
	
	// vérifie le type d'arguments du modèle (argument ou Macro) via le flag MS_EST_MACRO
	$req_type_modele = $bdd_supervision->prepare ( 'SELECT MS.MS_EST_MACRO FROM ' . $tbl_tmp_service . ' AS S INNER JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service WHERE S.ID_Service_Centreon = :ID_Service_Centreon' );
	$req_type_modele->execute ( Array (
			'ID_Service_Centreon' => $ID_Service_Centreon 
	) ) or die ( print_r ( $req_type_modele->errorinfo () ) );
	
	while ( $res_type_modele = $req_type_modele->fetch () ) // ne doit retourner qu'un seul enregistrement
{
		if ($res_type_modele [0] == 1) // Si MS_EST_MACRO = 1 les arguments sont de type MACRO
{
			$EST_MACRO = True;
			// addlog("EST_MACRO=".$EST_MACRO);
			
			// récupère les arguments de type Macro
			// 1) récupère la liste exhaustive des macro liées à la commande avec un maximum de 7 modèles (ce qui doit être largement suffisant)
			// +-----------------------------------------------------------------------------------------------------------+
			// | Macro |
			// +-----------------------------------------------------------------------------------------------------------+
			// | $_SERVICEINTERFACEID$ -w $_SERVICEWARNING$ -c $_SERVICECRITICAL$ -T $_SERVICEIFSPEED$ -S $_SERVICE64BITS$ |
			// +-----------------------------------------------------------------------------------------------------------+
			$req_Select_Macro = $bdd_centreon->prepare ( '
			SELECT TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))) AS Macro
			FROM service AS S
			LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
			LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
			LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
			LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
			LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
			LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
			LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
			LEFT JOIN command AS c on c.command_id = coalesce(S.command_command_id,T1.command_command_id,T2.command_command_id,T3.command_command_id,T4.command_command_id,T5.command_command_id,T6.command_command_id,T7.command_command_id)
			WHERE c.command_line IS NOT NULL
				AND TRIM(TRAILING SUBSTRING_INDEX(SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line)),"$",-1) FROM SUBSTRING(c.command_line,POSITION("$_SERVICE" IN c.command_line))) <> ""
				AND S.service_id= :ID_Service_Centreon' );
			$req_Select_Macro->execute ( Array (
					'ID_Service_Centreon' => $ID_Service_Centreon 
			) ) or die ( print_r ( $req_Select_Macro->errorinfo () ) );
			/*
			 * echo '<pre>';
			 * print_r($req_Select_Macro);
			 * echo '</pre>';
			 */
			// 2) extrait chaque Macro de la chaine
			$Chaine_Macro = "";
			while ( $res_Select_Macro = $req_Select_Macro->fetch () ) {
				$Chaine_Macro .= " " . htmlspecialchars ( $res_Select_Macro ['Macro'] );
			};
			// addlog("Chaine_Macro=".$Chaine_Macro);
			$T_Chaine_Macro = explode ( " ", TRIM ( $Chaine_Macro ) ); // découpe la chaine en tableau
			/*
			 * echo '<pre>';
			 * print_r($T_Chaine_Macro);
			 * echo '</pre>';
			 */
			$NbLigne = count ( $T_Chaine_Macro );
			$Liste_Macro = Array (); // recrée un nouveau tableau qui contiendra uniquement les noms des macro
			$i = 0;
			for($j = 0; $j < $NbLigne; $j ++) {
				// echo "ChaineMacro=".substr($T_Chaine_Macro[$j],0,9) . "\n";
				if (substr ( $T_Chaine_Macro [$j], 0, 9 ) == "\$_SERVICE") {
					// $Liste_Macro[$i] = substr($res_liste_Macro,9,-1); // retourne la valeur de la macro sans "$_SERVICE" et le dernier "$" et la stocke dans un nouveau tableau
					$Liste_Macro [$i] = $T_Chaine_Macro [$j]; // retourne la valeur de la macro et la stocke dans un nouveau tableau
					$i ++;
				};
			};
			/*
			 * echo '<pre>';
			 * print_r($Liste_Macro);
			 * echo '</pre>';
			 */
			// 3) récupérer la chaine des modèles afin de récupérer la liste des valeurs de chaque modèle
			// +------------+------------+------------+------------+------------+------------+------------+------------+
			// | service_id | service_id | service_id | service_id | service_id | service_id | service_id | service_id |
			// +------------+------------+------------+------------+------------+------------+------------+------------+
			// | 6405 | 7239 | 5325 | 878 | 5334 | NULL | NULL | NULL |
			// +------------+------------+------------+------------+------------+------------+------------+------------+
			
			$req_Liste_Modele = $bdd_centreon->prepare ( 'select DISTINCT T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id
				FROM service AS S
				LEFT JOIN service AS T1 on S.service_template_model_stm_id = T1.service_id
				LEFT JOIN service AS T2 on T1.service_template_model_stm_id = T2.service_id
				LEFT JOIN service AS T3 on T2.service_template_model_stm_id = T3.service_id
				LEFT JOIN service AS T4 on T3.service_template_model_stm_id = T4.service_id
				LEFT JOIN service AS T5 on T4.service_template_model_stm_id = T5.service_id
				LEFT JOIN service AS T6 on T5.service_template_model_stm_id = T6.service_id
				LEFT JOIN service AS T7 on T6.service_template_model_stm_id = T7.service_id
				LEFT JOIN on_demand_macro_service AS M on M.svc_svc_id=coalesce(T7.service_id,T6.service_id,T5.service_id,T4.service_id,T3.service_id,T2.service_id,T1.service_id,S.service_id)
				WHERE S.Service_id = :ID_Service_Centreon' );
			$req_Liste_Modele->execute ( Array (
					'ID_Service_Centreon' => $ID_Service_Centreon 
			) ) or die ( print_r ( $req_Liste_Modele->errorInfo () ) );
			// 4) boucle sur les id pour remplir chaque macro
			// on charge l'ensemble des valeur de macro
			$Macro = False; // indicateur
			$NbMacro = count ( $Liste_Macro );
			$Val_Macro = Array ();
			while ( $res_Liste_Modele = $req_Liste_Modele->fetch () ) // pour chaque service_id trouvé
{ // on recherche les valeurs de macro renseignée avec une boucle sur les 8 service_id
				for($k = 0; $k < 8; $k ++) {
					$svc_svc_id = htmlspecialchars ( $res_Liste_Modele [$k] );
					if (($svc_svc_id != NULL) or ($svc_svc_id != "")) // si le modèle n'est pas null, on traite
{
						$req_Macro_Valeur = $bdd_centreon->prepare ( 'SELECT svc_macro_name,svc_macro_value FROM on_demand_macro_service WHERE svc_svc_id= :svc_svc_id' );
						$req_Macro_Valeur->execute ( Array (
								'svc_svc_id' => $svc_svc_id 
						) ) or die ( print_r ( $req_Macro_Valeur->errorInfo () ) );
						$res_Macro_Valeur = $req_Macro_Valeur->fetchall ();
						/*
						 * echo '<pre>';
						 * print_r($res_Macro_Valeur);
						 * echo '</pre>';
						 */
						for($j = 0; $j < $NbMacro; $j ++) // pour chaque Liste_Macro
{
							// foreach ($res_Macro_Valeur AS $Macro_Name => $Macro_Valeur) // on boucle sur les valeurs remontée par la requête
							foreach ( $res_Macro_Valeur as $Macro_Name ) // on boucle sur les valeurs remontée par la requête
{
								// if (($Liste_Macro[$j] == $Macro_Name[0]) AND ($Macro_Name[1] != "")) // Si Liste_Macro = Macro_Name et MAcro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
								if ((strcasecmp ( $Liste_Macro [$j], $Macro_Name [0] ) == 0) and ($Macro_Name [1] != "")) // Si Liste_Macro = Macro_Name et Macro_Valeur non vide, on stocke la valeur dans le tableau Val_Macro
								                                                                                    // strcasecmp => comparaison insensible à la casse
								{
									$Val_Macro [$Macro_Name [0]] = substr ( $Macro_Name [0], 9, - 1 ) . ":" . $Macro_Name [1]; // tableau nommé, on stocke dans la valeur le nom puis ":" puis la valeur
										                                                                                 // exemple IFSPEED:1000
								};
							};
						};
					};
				};
			};
			/*
			 * echo '<pre>';
			 * print_r($res_Liste_Modele);
			 * echo '</pre>';
			 * echo '<pre>';
			 * print_r($Val_Macro);
			 * echo '</pre>';
			 */
			
			// 7) On construit la chaine des Macro selon le modèle des arguments
			$Chaine_Val_Macro = "";
			foreach ( $Val_Macro as $Macro_Nom => $Macro_Val ) {
				$Chaine_Val_Macro .= "!" . $Macro_Val;
			}
			;
			$Liste_Argument = $Chaine_Val_Macro . "#" . $Consigne_Sonde;
			$req_C_service = explode ( "#", $Liste_Argument ); // conversion de la chaine en tableau
			                                               
			// on insère les arguments en base
			/*
			 * echo '<pre>';
			 * print_r($req_C_service);
			 * echo '</pre>';
			 */
			$NbLigne = count ( $req_C_service );
			
			// $upd_service = $bdd_supervision->prepare('UPDATE ' . $tbl_tmp_service . ' SET Parametres= :Parametres, Consigne= :Consigne WHERE ID_Service_Centreon = :ID_Service_Centreon');
			$upd_service = $bdd_supervision->prepare ( 'UPDATE ' . $tbl_tmp_service . ' SET Parametres= :Parametres WHERE ID_Service_Centreon = :ID_Service_Centreon' );
			$upd_service->execute ( Array (
					'Parametres' => substr ( htmlspecialchars ( $req_C_service [0] ), 1 ), // stocke les arguments sans le premier !
					'ID_Service_Centreon' => $ID_Service_Centreon 
			) ) or die ( print_r ( $upd_service->errorInfo () ) );
		};
	};
};

// ///////////////////
// affichage des elements
// ///////////////////
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
		 FROM ' . $tbl_tmp_hote . '' );
$SEL_tmp_hote->execute ( Array () ) or die ( print_r ( $SEL_tmp_hote->errorInfo () ) );

$r_hote = $SEL_tmp_hote->fetchAll ();

$Nb_Hote = count ( $r_hote );
echo '<fieldset id=f_extraction_hote">';
echo '<legend>Liste des hôtes</legend>';
if ($Nb_Hote == 0) {
	echo '<p>Aucun résultat trouvé.</p>';
} else {
	echo '<table id="T_Liste_Hote" class="extraction_hote">';
	echo '<tr>';
	echo '<th>Hôte</th>';
	echo '<th>Description</th>';
	echo '<th>Adresse IP</th>';
	echo '<th>Type</th>';
	echo '<th>Localisation</th>';
	echo '<th>OS</th>';
	echo '<th>Architecture</th>';
	echo '<th>Langue</th>';
	echo '<th>Fonction</th>';
	echo '<th>Controle</th>';
	echo '<th hidden="hidden">host_id</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_hote as $res_hote ) // on boucle sur les valeurs remontée par la requête
{
		// while ($res_hote = $req_hote->fetch())
		// {
		echo '<tr>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Nom_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Description'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['IP_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Type_Hote'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['ID_Localisation'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['OS'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Architecture'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Langue'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_hote ['Fonction'] ) . '</td>';
		
		if (htmlspecialchars ( $res_hote ['Controle_Actif'] ) == "inactif") {
			echo '<td class="inactif">' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		} else {
			echo '<td>' . htmlspecialchars ( $res_hote ['Controle_Actif'] ) . '</td>';
		}
		;
		echo '<td hidden>h' . htmlspecialchars ( $res_hote ['ID_Hote_Centreon'] ) . '</td>';
		echo '</tr>';
		$i ++;
	}
	;
	echo '</table>';
};
echo '</fieldset>';

// ///////////////////
// affichage service
// ///////////////////
echo '<fieldset id="f_extraction_service">';
echo '<legend>Liste des services</legend>';
// include_once('remplissage_extraction_service.php');

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
	FROM ((' . $tbl_tmp_service . ' AS S 
		LEFT JOIN modele_service AS MS ON S.ID_Modele_Service=MS.ID_Modele_Service)
		LEFT JOIN ' . $tbl_tmp_hote . ' AS H ON S.ID_Hote_Centreon=H.ID_Hote_Centreon)
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

$liste_service = "";
$NbFieldset_Service = 1;
while ( $res_liste_service = $SEL_tmp_service->fetch () ) {
	if ($res_liste_service ['Controle_Actif'] == "actif")
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service">';
	} else 
	{
		echo '<fieldset id="Service' . $NbFieldset_Service . '" class="extraction_service inactif">';
	};
	echo '<legend>Service n°' . $NbFieldset_Service . '</legend>';
	echo '<!-- Nom service -->';
	// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Nom_Service'])) + 20*strlen(htmlspecialchars($res_liste_service['Nom_Service']))/100;
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Service'] ) ) + 10;
	echo '<label for="Nom_Service' . $NbFieldset_Service . '">Nom du service:</label>';
	echo '<input Readonly type="text" id="Nom_Service' . $NbFieldset_Service . '" name="Nom_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Service'] ) . '" size="' . $LongueurArg . '" maxlength="100"/>';
	echo ' ';
	echo '<!-- Hote du service -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) ) + 10;
	echo '<label for="Hote_Service' . $NbFieldset_Service . '">Hôte du service:</label>';
	echo '<input Readonly name="Hote_Service' . $NbFieldset_Service . '" id="Hote_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Hote'] ) . '" size="' . $LongueurArg . '" title="' . htmlspecialchars ( $res_liste_service ['IP_Hote'] ) . ' - ' . htmlspecialchars ( $res_liste_service ['ID_Localisation'] ) . '"/>  <!-- Liste Hote disponibles -->';
	echo ' ';
	echo '<br />';
	echo '<!-- Plage Horaire -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) ) + 10;
	echo '<label for="Service_Plage' . $NbFieldset_Service . '">Plage horaire de contrôle:</label>';
	echo '<input Readonly name="Service_Plage' . $NbFieldset_Service . '" id="Service_Plage' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Nom_Periode'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Service_Plage -->';
	echo ' ';
	echo '<!-- Modele service -->';
	$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) ) + 10;
	echo '<label for="Service_Modele' . $NbFieldset_Service . '">Modèle:</label>';
	echo '<input Readonly name="Service_Modele' . $NbFieldset_Service . '" id="Service_Modele' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['MS_Modele_Service'] ) . '" size="' . $LongueurArg . '"/>  <!-- Liste Type_Service -->';
	echo ' ';
	echo '<!-- Frequence -->';
	echo '<label for="Frequence_Service' . $NbFieldset_Service . '">Fréquence du controle:</label>';
	echo '<input Readonly type="text" id="Frequence_Service' . $NbFieldset_Service . '" name="Frequence_Service' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Frequence'] ) . '" size="20" maxlength="20"/> <br />';
	echo ' ';
	echo '<!-- Arguments -->';
	echo '<fieldset id="Arg_Service_Modele' . $NbFieldset_Service . '">';
	// echo '<legend>Arguments du service</legend>';
	// gestion des arguments
	include ('gestion_arguments.php');
	echo '</fieldset> <br /> ';
	/*
	 * echo '<fieldset id="Inactif_Arg_Service_Modele' . $NbFieldset_Service . '">';
	 * echo '<legend>Arguments du service initial</legend>';
	 * //gestion des arguments
	 * include('gestion_arguments.php');
	 * echo '</fieldset>';
	 */
	if (htmlspecialchars ( $res_liste_service ['Consigne'] ) != "") // s'il n'y a pas de consigne on n'affiche pas le champ
	{
		echo '<br />';
		echo '<!-- Service Consigne -->';
		// $LongueurArg= strlen(htmlspecialchars($res_liste_service['Consigne'])) + 20*strlen(htmlspecialchars($res_liste_service['Consigne']))/100;
		$LongueurArg = strlen ( htmlspecialchars ( $res_liste_service ['Consigne'] ) );
		echo '<label for="Service_Consigne' . $NbFieldset_Service . '">Lien vers la consigne :</label>';
		echo '<input Readonly type="text" id="Service_Consigne' . $NbFieldset_Service . '" name="Service_Consigne' . $NbFieldset_Service . '" value="' . htmlspecialchars ( $res_liste_service ['Consigne'] ) . '" size="' . $LongueurArg . '" maxlength="255"/> <br />';
	};

	// il n'y a pas de decription de consigne quelque soit le cas.
/*	echo '<!-- Service Consigne Description-->';
	echo '<label for="Consigne_Service_Detail' . $NbFieldset_Service . '">Description consigne :</label>';
	
	echo '<textarea Readonly id="Consigne_Service_Detail' . $NbFieldset_Service . '" name="Consigne_Service_Detail' . $NbFieldset_Service . '" rows="3" cols="50">' . htmlspecialchars ( $res_liste_service ['Detail_Consigne'] ) . '</textarea> <br />';
*/	
/*
	echo '<!-- Action à effectuer -->';
	echo '<label>Action à effectuer</label>';
	if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Creer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Créer"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Modifier") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Modifier"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Desactiver") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Désactiver"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Supprimer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Supprimer"/>';
	} else if (htmlspecialchars ( $res_liste_service ['Type_Action'] ) == "Activer") {
		echo '<input readonly name="Service_action' . $NbFieldset_Service . '" id="Service_action' . $NbFieldset_Service . '" value="Activer"/>';
	};
	echo '<br />';
	echo '<!-- Service Commentaire -->';
	echo '<label for="Service_Commentaire' . $NbFieldset_Service . '">Commentaire :</label>';
	echo '<textarea readonly id="Service_Commentaire' . $NbFieldset_Service . '" name="Service_Commentaire' . $NbFieldset_Service . '" rows="3" cols="50" class="service' . $NbFieldset_Service . '">' . htmlspecialchars ( $res_liste_service ['Commentaire'] ) . '</textarea> <br />';

	if ($_SESSION ['Admin'] == True) // si admin affichage liste déroulante etat + bouton enregistrer
	{
		$ID_Service = htmlspecialchars ( $res_liste_service ['ID_Service'] );
		include ('insere_fieldset_Admin_Service.php');
	};
*/
	echo '</fieldset>';

	$NbFieldset_Service ++;
}
;
$Statut_Service = true;
echo '</fieldset>';


// ///////////////////
// affichage periode
// ///////////////////

$SEL_tmp_periode = $bdd_supervision->prepare ( 'SELECT Nom_Periode, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM ' . $tbl_tmp_periode . '' );
$SEL_tmp_periode->execute ( Array () ) or die ( print_r ( $SEL_tmp_periode->errorInfo () ) );

$r_plage = $SEL_tmp_periode->fetchAll ();

$nb_plage = count ( $r_plage );
echo '<fieldset id="f_extraction_periode">';
echo '<legend>Liste des périodes temporelles</legend>';
if ($nb_plage == 0) {
	echo '<p>Aucun résultat trouvé.</p>';
} else
{
	echo '<table id="T_Liste_Plage" class="extraction_periode">';
	echo '<tr>';
	echo '<th>Plage Horaire</th>';
	echo '<th>Lundi</th>';
	echo '<th>Mardi</th>';
	echo '<th>Mercredi</th>';
	echo '<th>Jeudi</th>';
	echo '<th>Vendredi</th>';
	echo '<th>Samedi</th>';
	echo '<th>Dimanche</th>';
	echo '</tr>';
	$i = 1;
	foreach ( $r_plage as $res_plage ) 
	// while ($res_plage = $req_plage->fetch())
	{
		echo '<tr>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Nom_Periode'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Lundi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Mardi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Mercredi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Jeudi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Vendredi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Samedi'] ) . '</td>';
		echo '<td>' . htmlspecialchars ( $res_plage ['Dimanche'] ) . '</td>';
		echo '</tr>';
		$i ++;
	}
	;
	echo '</table>';
};
echo '</fieldset>';

// // gestion pour impression
//echo '<a href="javascript:window.print()">Exporter</a>';
//