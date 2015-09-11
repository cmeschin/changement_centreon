<?php

try {
	$req_etat = $bdd_supervision->query('SELECT
			 Etat_Dem,
			 etat_class
			 FROM etat_demande
			 ORDER BY ID_Etat_Dem'
		) or die(print_r($req_etat->errorInfo()));
} catch (Exception $e) {
	die('Erreur requete liste etat demande: ' . $e->getMessage());
}
	