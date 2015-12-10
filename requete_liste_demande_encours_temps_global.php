<?php
$req_dem_tg = $bdd_supervision->prepare(
		'SELECT
		 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
	FROM demande
	WHERE Etat_Demande NOT IN ("Traité", "Annulé")');
$req_dem_tg->execute(array());