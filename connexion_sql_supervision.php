<?php
try
{
	$bdd_supervision = new PDO('mysql:host=localhost;dbname=CHGT_Centreon', 'changement_RW', 'changement_RW', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch (Exception $e)
{
    http_response_code(500);
	die('Erreur de connexion à CHGT_Centreon: ' . $e->getMessage());
}
