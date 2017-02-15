<?php
//session_start();
/**
 * Automate d'import des données de suivi des consignes issue de la base centreon.
 * Nb consignes directes
 * Nb de consignes par modèles
 * Nb de consignes par service issues des modèles
 * Nb de service total hors modèles
 */

/**
 * Initialisation des constantes
 * Date Import
 */
	include_once('connexion_sql_supervision.php'); // connexion à la base changement
	include_once('connexion_sql_centreon.php'); // connexion à la base centreon
	$dateImport = date("Y-m-d H:i:s");
		
/**
 * extraction des données
 */
$reqConsigne = $bdd_centreon->prepare('
	SELECT
		 (SELECT count(1) FROM service AS s RIGHT JOIN extended_service_information AS esi ON s.service_id=esi.service_service_id LEFT JOIN host_service_relation AS hsr ON s.service_id=hsr.service_service_id LEFT JOIN host AS h ON hsr.host_host_id=h.host_id WHERE esi.esi_notes_url IS NOT NULL AND s.service_register="1" AND s.service_activate="1" AND h.host_activate="1") AS nbDirect,
		 (SELECT count(1) FROM service AS s RIGHT JOIN extended_service_information AS esi ON s.service_id=esi.service_service_id WHERE esi.esi_notes_url IS NOT NULL AND s.service_register="0" AND s.service_activate="1" AND s.service_locked="0") AS nbModel,
		 (SELECT count(1) FROM service AS s WHERE s.service_id NOT IN ( SELECT s1.service_id FROM service AS s1 RIGHT JOIN extended_service_information AS esi ON s1.service_id=esi.service_service_id LEFT JOIN host_service_relation AS hsr ON s1.service_id=hsr.service_service_id LEFT JOIN host AS h ON hsr.host_host_id=h.host_id WHERE esi.esi_notes_url IS NOT NULL AND s1.service_register="1" AND s1.service_activate="1" AND s1.service_locked="0") AND s.service_template_model_stm_id IN (SELECT s2.service_id FROM service AS s2 RIGHT JOIN extended_service_information AS esi ON s2.service_id=esi.service_service_id WHERE esi.esi_notes_url IS NOT NULL AND s2.service_register="0" AND s2.service_activate="1")) AS nbIndirect,
		 (SELECT count(1) FROM service AS s LEFT JOIN host_service_relation AS hsr ON s.service_id=hsr.service_service_id LEFT JOIN host AS h ON hsr.host_host_id=h.host_id WHERE service_register="1" AND service_activate="1" AND h.host_activate="1") AS nbTotal;');
$reqConsigne->execute(array()) or die(print_r($reqConsigne->errorInfo()));

/**
 * Purge des données collectées supérieurs à 365 jours
 */

$purge = $bdd_supervision->prepare('
		DELETE FROM suivi_consigne
		WHERE sc_date <= DATE_ADD(Now(),INTERVAL -365 DAY);');
$purge->execute(array()) or die(print_r($purge->errorInfo()));

/**
 * Insertion des nouvelles données.
 */
while ($resConsigne=$reqConsigne->fetch())
{
	$insert = $bdd_supervision->prepare('
		INSERT INTO suivi_consigne
			(sc_date, sc_direct, sc_model, sc_indirect, sc_total)
			VALUES (:date, :direct, :model, :indirect, :total);');
	$insert->execute(array(
			'date' => $dateImport,
			'direct' => htmlspecialchars($resConsigne['nbDirect']),
			'model' => htmlspecialchars($resConsigne['nbModel']),
			'indirect' => htmlspecialchars($resConsigne['nbIndirect']),
			'total' => htmlspecialchars($resConsigne['nbTotal'])
	)) or die(print_r($insert->errorInfo()));

};
