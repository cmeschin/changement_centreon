<?php

// Connexion à la base de données supervision
try
{
//	$bdd_supervision = new PDO('mysql:host=10.33.1.80;dbname=CHGT_Centreon', 'changement_RW', 'changement_RW', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$bdd_supervision = new PDO('mysql:host=localhost;dbname=CHGT_Centreon', 'changement_RW', 'changement_RW', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	//echo 'connexion';
}
catch (Exception $e)
{
    http_response_code(500);
	die('Erreur : ' . $e->getMessage());
}
