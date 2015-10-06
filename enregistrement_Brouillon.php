<?php
include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();
	include('enregistrement_donnees.php');
	// Mise à jour de l'état de la demande en "Brouillon"
	$MAJ_Demande = $bdd_supervision->prepare('UPDATE demande SET Etat_Demande= "Brouillon" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Demande->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Demande->errorInfo()));
	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= "Brouillon" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Hote->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Hote->errorInfo()));
	$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Etat_Parametrage= "Brouillon" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Service->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Service->errorInfo()));
	$MAJ_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle SET Etat_Parametrage= "Brouillon" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Plage->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Plage->errorInfo()));
	addlog("Enregistrement brouillon termine");
	/**
	 * Mise à jour de la variable Timer
	 */
	$date=date_create();
	$_SESSION['Timer']=date_timestamp_get($date);
	
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur enregistrement brouillon: '. $e->getMessage());
};
	