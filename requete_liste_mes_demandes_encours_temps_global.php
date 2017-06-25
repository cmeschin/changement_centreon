<?php
$req_dem_tg = $bdd_supervision->prepare(
	'SELECT
		 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
	FROM demande
	WHERE Etat_Demande NOT IN ("Traité", "Annulé")
		AND demandeur= :user');
$req_dem_tg->execute(array(
		'user' => htmlspecialchars($_SESSION['user_changement_centreon'])
)) or die(print_r($req_dem_tg->errorInfo()));