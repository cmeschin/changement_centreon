<?php
$req_modele = $bdd_supervision->prepare('SELECT
	ID_Modele_Service,
	Modele_Service
FROM modele_service
WHERE Modele_Service NOT LIKE "_CENTREON_%"
ORDER BY Modele_Service');
$req_modele->execute(Array()) or die(print_r($req_modele->errorInfo()));
