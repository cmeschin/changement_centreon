<?php
//echo 'etat=' . $etat_dem;
try {
// 	$req_etat = $bdd_supervision->query('SELECT
// 			 Etat_Dem,
// 			 etat_class
// 			 FROM etat_demande
// 			 ORDER BY ID_Etat_Dem'
// 		) or die(print_r($req_etat->errorInfo()));
	$req_etat = $bdd_supervision->query('SELECT
			 DISTINCT ed.etat_dem as Etat_Dem
			 FROM etat_demande AS ed LEFT JOIN relation_etat_demande AS red ON ed.ID_etat_dem=red.red_etat_parent_id
			 LEFT JOIN etat_demande AS ped ON red.red_etat_id=ped.id_etat_dem
			 WHERE ped.etat_dem="' . $etat_dem . '" OR ed.Etat_dem="' . $etat_dem . '"
			 ORDER BY ed.id_etat_dem'
		) or die(print_r($req_etat->errorInfo()));
} catch (Exception $e) {
	die('Erreur requete liste etat demande: ' . $e->getMessage());
}