<?php
try
{
	$bdd_centreon = new PDO('mysql:host=localhost;dbname=centreon', 'centreon_RO', 'centreon_RO', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch (Exception $e)
{
	http_response_code(500);
	die('Erreur de connexion à Centreon: ' . $e->getMessage());
};
