<?php
if (session_id()=='')
{
	session_start();
};
include('connexion_sql_centreon.php'); 
include('connexion_sql_supervision.php'); 
include('log.php'); // chargement de la fonction de log

try {
	$bdd_supervision->beginTransaction();
	/**
	 * Mise à jour de la table modele_centreon
	 */
	$Del_Modele_centreon = $bdd_supervision->query('TRUNCATE table modele_centreon') or die(print_r($Del_Modele_centreon->errorInfo()));
	
	$req_mod_centreon = $bdd_centreon->query('SELECT DISTINCT(service_description),service_alias,service_id FROM service WHERE service_id IN (SELECT service_template_model_stm_id FROM service) AND service_locked=0 ORDER BY service_description') or die(print_r($req_mod_centreon->errorInfo()));
	
	while($res_mod_centreon = $req_mod_centreon->fetch())
	{
		$ins_modele_centreon = $bdd_supervision->prepare('INSERT INTO modele_centreon (service_description,service_alias,service_id) VALUES (:service_description,:service_alias,:service_id)');
		$ins_modele_centreon->execute(Array(
			'service_description' => htmlspecialchars($res_mod_centreon['service_description']),
			'service_alias' => htmlspecialchars($res_mod_centreon['service_alias']),
			'service_id' => htmlspecialchars($res_mod_centreon['service_id'])
		)) or die(print_r($ins_modele_centreon->errorInfo()));
	}; 
	
	/**
	 * Nettoyage des relations modele_service <=> modele_centreon
	 */
	$Nettoyage_relation = $bdd_supervision->query('
			DELETE FROM relation_modeles WHERE id_modele_service NOT IN (SELECT ID_Modele_service FROM modele_service);
			') or die(rpint_r($Nettoyage_relation->errorinfo()));
	
	/**
	 * Mise à jour des tables hote_os
	 */
	$value_hote = "";
	$Del_hote_os = $bdd_supervision->query('TRUNCATE table hote_os') or die(print_r($Del_hote_os->errorInfo()));
	
	$req_hote_os = $bdd_centreon->query('SELECT SUBSTRING(hc_name,4) as hc_name, hc_alias FROM hostcategories where hc_name LIKE "OS_%" ORDER BY hc_name')
		or die(print_r($req_hote_os->errorInfo()));
	
	$res_hote_os = $req_hote_os->fetchAll ();
	foreach ( $res_hote_os as $res_elements )
	{
		$value_hote .= ",('" . $res_elements['hc_name'] . "','" . $res_elements['hc_alias'] . "')";
	};
	
	$value_hote = substr($value_hote,1); // suppression de la première virgule
	addlog($value_hote);
	$insert_hote_os = $bdd_supervision->prepare (
			'INSERT INTO hote_os (Type_OS, Type_OS_Desc) VALUES ' . $value_hote . '');
	$insert_hote_os->execute(array()) or die(print_r($insert_hote_os->errorInfo()));

	
	/**
	 * Mise à jour de la table localisation
	 */
	$value_site = "";
	$Del_site = $bdd_supervision->query('TRUNCATE table localisation') or die(print_r($Del_site->errorInfo()));
	
	$req_site = $bdd_centreon->query('SELECT substring(hg_alias,1,4) as site_abrege, substring(hg_name,6) as site FROM hostgroup WHERE hg_name LIKE "Site%"')
		or die(print_r($req_site->errorInfo()));
	
	$res_site = $req_site->fetchAll ();
	foreach ( $res_site as $res_elements )
	{
		$value_site .= ",('" . $res_elements['site_abrege'] . "','" . $res_elements['site'] . "')";
	};
	
	$value_site = substr($value_site,1); // suppression de la première virgule
	addlog($value_site);
	$insert_site = $bdd_supervision->prepare (
			'INSERT INTO localisation (ID_Localisation, Lieux) VALUES ' . $value_site . '');
	$insert_site->execute(array()) or die(print_r($insert_site->errorInfo()));

	
	/**
	 * Mise à jour des tables hote_type
	 */
	$value_hote = "";
	$Del_hote_type = $bdd_supervision->query('TRUNCATE table hote_type') or die(print_r($Del_hote_type->errorInfo()));
	
	$req_hote_type = $bdd_centreon->query('SELECT SUBSTRING(hc_name,6) as hc_name, hc_alias FROM hostcategories where hc_name LIKE "Type_%" ORDER BY hc_name')
	or die(print_r($req_hote_type->errorInfo()));
	
	$res_hote_type = $req_hote_type->fetchAll ();
	foreach ( $res_hote_type as $res_elements )
	{
		$value_hote .= ",('" . $res_elements['hc_alias'] . "','" . $res_elements['hc_name'] . "')";
	};
	
	$value_hote = substr($value_hote,1); // suppression de la première virgule
	addlog($value_hote);
	$insert_hote_type = $bdd_supervision->prepare (
			'INSERT INTO hote_type (Type_Hote, Type_Description) VALUES ' . $value_hote . '');
	$insert_hote_type->execute(array()) or die(print_r($insert_hote_type->errorInfo()));


	/**
	 * Mise à jour des tables hote_fonction
	 */
	$value_hote = "";
	$Del_hote_fonction = $bdd_supervision->query('TRUNCATE table hote_fonction') or die(print_r($Del_hote_fonction->errorInfo()));
	
	$req_hote_fonction = $bdd_centreon->query('SELECT SUBSTRING(hc_name,10) as hc_name, hc_alias FROM hostcategories where hc_name LIKE "Fonction_%" ORDER BY hc_name')
	or die(print_r($req_hote_fonction->errorInfo()));
	
	$res_hote_fonction = $req_hote_fonction->fetchAll ();
	foreach ( $res_hote_fonction as $res_elements )
	{
		$value_hote .= ",('" . $res_elements['hc_name'] . "','" . $res_elements['hc_alias'] . "')";
	};
	
	$value_hote = substr($value_hote,1); // suppression de la première virgule
	addlog($value_hote);
	$insert_hote_fonction = $bdd_supervision->prepare (
			'INSERT INTO hote_fonction (hote_fonction, hote_fonction_desc) VALUES ' . $value_hote . '');
	$insert_hote_fonction->execute(array()) or die(print_r($insert_hote_fonction->errorInfo()));

	/**
	 * Mise à jour de la table mob_bam_centreon à partir de la table mod_bam de centreon
	 */
	$value_bam = "";
	$Del_mod_bam_centreon = $bdd_supervision->query('TRUNCATE table mod_bam_centreon') or die(print_r($Del_mod_bam_centreon->errorInfo()));
	
	include_once('requete_BAM_liste_AM.php');
	
	$res_lst_bam = $req_lst_bam->fetchAll ();
	foreach ( $res_lst_bam as $res_elements )
	{
		$value_bam .= ",('" . $res_elements['ba_id'] . "','" . $res_elements['ba_nom'] . "','" . $res_elements['ba_description'] . "')";
	};
	
	$value_bam = substr($value_bam,1); // suppression de la première virgule
	addlog($value_bam);
	$insert_mod_bam_centreon = $bdd_supervision->prepare (
			'INSERT INTO mod_bam_centreon (mbc_ba_id, mbc_ba_nom, mbc_ba_description) VALUES ' . $value_bam . '');
	$insert_mod_bam_centreon->execute(array()) or die(print_r($insert_mod_bam_centreon->errorInfo()));
	
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur MAJ_modele_centreon: ' . $e->getMessage());
};
