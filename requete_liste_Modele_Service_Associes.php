<?php
//include_once('connexion_sql_supervision.php');

$req_modele_associes = $bdd_supervision->prepare('SELECT ID_Modele_Service,
		 Modele_Service
		 FROM modele_service
		 ORDER BY ID_Modele_Service');
$req_modele_associes->execute(Array()) or die(print_r($req_modele_associes->errorInfo()));
