<?php

// Connexion à la base de données
try
{
//	$bdd_centreon = new PDO('mysql:host=10.33.3.23;dbname=centreon', 'centreon_RO', 'centreon_RO', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$bdd_centreon = new PDO('mysql:host=localhost;dbname=centreon', 'centreon_RO', 'centreon_RO', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//	echo 'connexion';
}
catch (Exception $e)
{
    http_response_code(500);
	die('Erreur : ' . $e->getMessage());
}
