<?php
$req_modele_nonassocies = $bdd_supervision->prepare('SELECT 
		 service_id,
		 service_description
		 FROM modele_centreon
		 WHERE service_id NOT IN (SELECT
			 ID_Modele_Service_Centreon
			 FROM relation_modeles)
		 ORDER BY service_description');
$req_modele_nonassocies->execute(Array()) or die(print_r($req_modele_nonassocies->errorInfo()));
