<?php
$req_dem_tg = $bdd_supervision->prepare(
		'SELECT
		 CONCAT(FLOOR(sum(temps_hote + temps_service)/60),"h",LPAD(sum(temps_hote + temps_service)%60,2,"00")) AS Temps_Global
		 FROM Demande
		 WHERE Etat_Demande IN ("Traité", "Annulé")
		 AND CONCAT(SUBSTRING(Date_Demande,1,4),SUBSTRING(Date_Demande,6,2))= :date_demande_groupee');
$req_dem_tg->execute(array(
		'date_demande_groupee' => htmlspecialchars($sID_Date)
)) or die(print_r($req_dem_tg->errorInfo()));