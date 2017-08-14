<?php
try 
{
	$req_etat = $bdd_supervision->query('SELECT
			 DISTINCT ed.etat_dem as Etat_Dem,
			 ed.id_etat_dem as id,
			 "actif" as statut
			 FROM etat_demande AS ed
				 LEFT JOIN relation_etat_demande AS red ON ed.ID_etat_dem=red.red_etat_parent_id
				 LEFT JOIN etat_demande AS ped ON red.red_etat_id=ped.id_etat_dem
			 WHERE ped.etat_dem="' . $etat_dem . '" OR ed.Etat_dem="' . $etat_dem . '"
			 UNION SELECT
			 DISTINCT etat_dem as Etat_Dem,
			 id_etat_dem as id,
			 "inactif" as statut
			 FROM etat_demande
			 WHERE etat_dem NOT IN (SELECT
				 DISTINCT ed.etat_dem as Etat_Dem
				 FROM etat_demande AS ed
				 LEFT JOIN relation_etat_demande AS red ON ed.ID_etat_dem=red.red_etat_parent_id
				 LEFT JOIN etat_demande AS ped ON red.red_etat_id=ped.id_etat_dem
				 WHERE ped.etat_dem="' . $etat_dem . '" OR ed.Etat_dem="' . $etat_dem . '"
				 ORDER BY ed.id_etat_dem)
			 ORDER BY id'
			
// 			SELECT
// 			 DISTINCT ed.etat_dem as Etat_Dem
// 			 FROM etat_demande AS ed LEFT JOIN relation_etat_demande AS red ON ed.ID_etat_dem=red.red_etat_parent_id
// 			 LEFT JOIN etat_demande AS ped ON red.red_etat_id=ped.id_etat_dem
// 			 WHERE ped.etat_dem="' . $etat_dem . '" OR ed.Etat_dem="' . $etat_dem . '"
// 			 ORDER BY ed.id_etat_dem'
		) or die(print_r($req_etat->errorInfo()));
} catch (Exception $e) {
	die('Erreur requete liste etat demande: ' . $e->getMessage());
}