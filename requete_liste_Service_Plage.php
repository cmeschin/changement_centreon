<?php
if (session_id()=='')
{
session_start();
};// récupération de la ref demande
//$Code_Client= $_SESSION['Code_Client'];
$ID_Demande= $_SESSION['ID_dem'];

$req_Service_Plage = $bdd_supervision->prepare('SELECT
		 DISTINCT(Nom_Periode)
		 FROM periode_temporelle
		 WHERE ID_Demande = :ID_Demande
		 ORDER BY Nom_Periode');
$req_Service_Plage -> execute(Array(
	'ID_Demande' => $ID_Demande
)) or die(print_r($req_Service_Plage->errorInfo()));
