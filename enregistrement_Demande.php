<?php
include_once('connexion_sql_supervision.php');
try {
	$bdd_supervision->beginTransaction();

	include('enregistrement_donnees.php');
	// Mise à jour de l'état de la demande en "A traiter"
	$MAJ_Demande = $bdd_supervision->prepare('UPDATE demande SET Etat_Demande= "A Traiter" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Demande->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Demande->errorInfo()));
	//$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= "A Traiter" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Hote = $bdd_supervision->prepare('UPDATE hote SET Etat_Parametrage= "A Traiter" WHERE Type_Action <> "NC" AND ID_Demande= :ID_Demande;');
	$MAJ_Hote->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Hote->errorInfo()));
	$MAJ_Service = $bdd_supervision->prepare('UPDATE service SET Etat_Parametrage= "A Traiter" WHERE ID_Demande= :ID_Demande;');
	$MAJ_Service->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Service->errorInfo()));
	$MAJ_Plage = $bdd_supervision->prepare('UPDATE periode_temporelle SET Etat_Parametrage= "A Traiter" WHERE Type_Action <> "NC" AND ID_Demande= :ID_Demande;');
	$MAJ_Plage->execute(array(
		'ID_Demande' => $ID_Demande
		)) or die(print_r($MAJ_Plage->errorInfo()));
	
	///////////////////////////////////////////////////////
	// Gestion du temps de traitement de la demande
	///////////////////////////////////////////////////////
	//##########################################################
	//# boucle à désactiver après la première nouvelle demande #
	//##########################################################
	
	//##############################################################################
	//$SELECT_ID_Dem =$bdd_supervision->prepare('SELECT ID_Demande FROM demande;');
	//$SELECT_ID_Dem->execute(Array()) or die(print_r($SELECT_ID_Dem->errorInfo()));
	//While ($res_ID_Dem = $SELECT_ID_Dem->fetch())
	//{
	// $ID_Demande = $res_ID_Dem[0];
	//###############################################################################
	$UPD_Dem_Hote = $bdd_supervision->prepare('UPDATE demande SET temps_hote=(SELECT SUM(Temps_Hote) FROM (SELECT CASE H.Type_Action WHEN "Creer" THEN count(H.ID_Hote) * 30 WHEN "Modifier" THEN count(H.ID_Hote) * 5 WHEN "Desactiver" THEN count(H.ID_Hote) * 2 WHEN "Supprimer" THEN count(H.ID_Hote) * 2 WHEN "" THEN count(H.ID_Hote) * 5 END AS Temps_Hote FROM demande AS D INNER JOIN hote AS H ON D.ID_Demande=H.ID_Demande WHERE D.ID_Demande= :ID_Demande1 GROUP BY D.ID_Demande, H.Type_Action ORDER BY D.ID_Demande ASC ) as Tps_Total) where ID_Demande= :ID_Demande2');
	$UPD_Dem_Hote->execute(Array(
			'ID_Demande1' => $ID_Demande,
			'ID_Demande2' => $ID_Demande
	)) or die(print_r($UPD_Dem_Hote->errorInfo()));
	
	$UPD_Dem_Hote2 = $bdd_supervision->prepare('UPDATE demande SET temps_hote=0 WHERE temps_hote IS NULL AND ID_Demande= :ID_Demande');
	$UPD_Dem_Hote2->execute(Array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Dem_Hote2->errorInfo()));
	
	$UPD_Dem_Service = $bdd_supervision->prepare('UPDATE demande SET temps_service=(SELECT SUM(Temps_Service) FROM (SELECT CASE S.Type_Action WHEN "Creer" THEN count(S.ID_Service) * 5 WHEN "Modifier" THEN count(S.ID_Service) * 3 WHEN "Desactiver" THEN count(S.ID_Service) * 2 WHEN "Supprimer" THEN count(S.ID_Service) * 2 WHEN "" THEN count(S.ID_Service) * 3 END AS Temps_Service FROM demande AS D INNER JOIN service AS S ON D.ID_Demande=S.ID_Demande WHERE D.ID_Demande= :ID_Demande1 GROUP BY D.ID_Demande, S.Type_Action ORDER BY D.ID_Demande ASC ) as Tps_Total) where ID_Demande= :ID_Demande2');
	$UPD_Dem_Service->execute(Array(
			'ID_Demande1' => $ID_Demande,
			'ID_Demande2' => $ID_Demande
	)) or die(print_r($UPD_Dem_Service->errorInfo()));
	
	$UPD_Dem_Service2 = $bdd_supervision->prepare('UPDATE demande SET temps_service=0 WHERE temps_service IS NULL AND ID_Demande= :ID_Demande');
	$UPD_Dem_Service2->execute(Array(
			'ID_Demande' => $ID_Demande
	)) or die(print_r($UPD_Dem_Service2->errorInfo()));
	
	//###############################################################################
	//};
	//###############################################################################
	
	addlog("Enregistrement Demande termine");
	// envoie du mail à SUSI
		include('envoi_mail.php');
	addlog("Mail envoyé");
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur enregistrement demande: '. $e->getMessage());
};