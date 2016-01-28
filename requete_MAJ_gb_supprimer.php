<?php
if (session_id()=='')
{
	session_start();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log

try {
	include_once('connexion_sql_supervision.php');
	$bdd_supervision->beginTransaction();
	$gb_id = (isset($_POST["gb_id"])) ? $_POST["gb_id"] : NULL;
	
	if ($gb_id != NULL)
	{
		$req_bam = $bdd_supervision->prepare(
			'DELETE FROM gestion_bam_notification WHERE gb_id= :gb_id;'
			);
		$req_bam->execute(array(
			'gb_id' => htmlspecialchars($gb_id)
		)) or die(print_r($req_bam->errorInfo()));
		
	};
	$bdd_supervision->commit();
	addlog("Suppression notification BAM id=" . $gb_id ."");
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur insertion Selection: '. $e->getMessage());
};
