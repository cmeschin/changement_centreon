<?php
$req_dem_groupee = $bdd_supervision->prepare(
		'SELECT
 			CONCAT(substring(Date_Demande,1,4),substring(Date_Demande,6,2)) as "ID_Date",
			substring(Date_Demande,1,7) as "Date",
			count(date_demande) as "Nombre"
		 FROM demande
		 WHERE Etat_Demande IN ("Traité", "Annulé")
			AND Demandeur <> :user
		 GROUP BY ID_Date,Date_Demande
		 ORDER BY substring(Date_Demande,1,7) DESC');
$req_dem_groupee->execute(array(
		'user' => htmlspecialchars($_SESSION['user_changement_centreon'])
)) or die(print_r($req_dem_groupee->errorInfo()));
