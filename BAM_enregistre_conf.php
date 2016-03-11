<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement BAM_Enregistrement_conf.php");

include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();
	$sliste_conf = (isset($_POST["liste_conf"])) ? $_POST["liste_conf"] : NULL;
	
	$liste_conf = explode("|",$sliste_conf); // découpe la chaine en tableau avec comme séparateur le |
	
	if ($liste_conf[0] != "") // si le gb_nom est renseigné on traite les données 
	{
		addlog("gb_nom=".($liste_conf[0]));

		addlog("INSERT Table gestion_bam_notification");
		$MAJ_conf = $bdd_supervision->prepare('INSERT IGNORE INTO gestion_bam_notification 
			(gb_nom, gb_mail_objet, gb_mail_titre, gb_mail_liste, gb_lundi, gb_mardi, gb_mercredi, gb_jeudi, gb_vendredi, gb_samedi, gb_dimanche, gb_heure)
			VALUES (:gb_nom, :gb_mail_objet, :gb_mail_titre, :gb_mail_liste, :gb_lundi, :gb_mardi, :gb_mercredi, :gb_jeudi, :gb_vendredi, :gb_samedi, :gb_dimanche, :gb_heure)');
		$MAJ_conf->execute(array(
			'gb_nom' => htmlspecialchars($liste_conf[0]),
			'gb_mail_objet' => htmlspecialchars($liste_conf[1]),
			'gb_mail_titre' => htmlspecialchars($liste_conf[2]),
			'gb_mail_liste' => htmlspecialchars($liste_conf[3]),
			'gb_lundi' => htmlspecialchars($liste_conf[4]),
			'gb_mardi' => htmlspecialchars($liste_conf[5]),
			'gb_mercredi' => htmlspecialchars($liste_conf[6]),
			'gb_jeudi' => htmlspecialchars($liste_conf[7]),
			'gb_vendredi' => htmlspecialchars($liste_conf[8]),
			'gb_samedi' => htmlspecialchars($liste_conf[9]),
			'gb_dimanche' => htmlspecialchars($liste_conf[10]),
			'gb_heure' => htmlspecialchars($liste_conf[11])
		)) or die(print_r($MAJ_conf->errorInfo()));
	};

/**
 * Récuparation du gb_id pour insertion
 */	
	$req_gb_id = $bdd_supervision->prepare('SELECT gb_id FROM gestion_bam_notification WHERE gb_nom= :gb_nom');
	$req_gb_id->execute(array(
			'gb_nom' => htmlspecialchars($liste_conf[0])
	)) or die(print_r($req_gb_id->errorInfo()));

	
  	$res_gb_id = $req_gb_id->fetchall();
 	foreach ($res_gb_id AS $valeur)
 	{ // on la valeur de la requête valeurs remontée par la requête 
 		$gb_id=$valeur[0];
 	};
 	/**
 	 * On insère finalement les am sélectionnées dans la table de relation.
 	 */
 	/**
 	 * Découpage en tableau de la liste des am
 	 */
 	$liste_T_conf = explode("$",$liste_conf[12]);
 	$Nbam = count($liste_T_conf);
 	addlog("Nbam=".$Nbam);
 	$value_gba = "";
 	for ( $i=0;$i<$Nbam;$i++ )
 	{
 		addlog("liste_T_conf=".($liste_T_conf[$i]));
 		$value_gba .= ",(" . $gb_id . "," . $liste_T_conf[$i] . ")";
 	};
 	$value_gba = substr($value_gba,1); // suppression de la première virgule
 	addlog($value_gba);
 	$insert_gba = $bdd_supervision->prepare (
 			'INSERT IGNORE INTO gestion_bam_associe (gba_gb_id, gba_ba_id) VALUES ' . $value_gba . '');
 	$insert_gba->execute(array()) or die(print_r($insert_gba->errorInfo()));

 	/**
 	 * MAJ table gestion_bam_notification pour insertion liste_bam
 	 */
 	$liste_am = str_replace("$", ",", $liste_conf[12]);
 	/**
 	 * récupère la liste texte des AM
 	 */
 	$req_am = $bdd_supervision->prepare(
 			'SELECT mbc_ba_id,
 				 mbc_ba_nom
 			 FROM mod_bam_centreon
 			 WHERE mbc_ba_id IN (' . $liste_am . ')
 			 ORDER BY mbc_ba_nom;');
 	$req_am->execute(array()) or die(print_r($req_am->errorinfo()));
 	$res_am = $req_am->fetchAll();
 	$liste_nom = "";
 	foreach($res_am as $valeur)
 	{
 		$liste_nom .= ", " . $valeur['mbc_ba_nom'];
 	};
 	// on supprime la première virgule
 	$liste_nom = substr($liste_nom,2);
 	addlog("liste_nom=" . $liste_nom);
 	$insert_gba = $bdd_supervision->prepare('UPDATE gestion_bam_notification SET gb_bam_liste="' . $liste_nom . '" WHERE gb_id = ' . $gb_id . ';');
 	$insert_gba->execute(array()) or die(print_r($insert_gba->errorInfo()));
 	addlog("traitement gestion BAM associé...OK");
 	
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur Enregistrement_conf: ' . $e->getMessage());
};
