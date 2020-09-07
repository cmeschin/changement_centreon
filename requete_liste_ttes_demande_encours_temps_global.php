<?php
$req_dem_tg = $bdd_supervision->prepare(
	'SELECT
		(SELECT
		 	CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
			FROM demande
			WHERE Etat_Demande NOT IN ("Brouillon", "Traité", "Annulé") AND date_supervision_demandee <= NOW()) AS Temps_J
		,(SELECT
			CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
			FROM demande
			WHERE Etat_Demande NOT IN ("Brouillon", "Traité", "Annulé") AND date_supervision_demandee <= date_add(NOW(),INTERVAL 7 DAY)) AS Temps_J7
		,CONCAT(FLOOR(SUM(temps_hote + temps_service)/60),"h",LPAD(SUM(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
	FROM demande
	WHERE Etat_Demande NOT IN ("Brouillon", "Traité", "Annulé")
');
$req_dem_tg->execute() or die(print_r($req_dem_tg->errorInfo()));