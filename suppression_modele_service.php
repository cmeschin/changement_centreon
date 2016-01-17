<?php
session_start();
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
addlog("Chargement suppression_modele_service.php");

include_once('connexion_sql_supervision.php');
$sID_Modele_Service = (isset($_POST["ID_Modele_Service"])) ? $_POST["ID_Modele_Service"] : NULL;

try {
	$bdd_supervision->beginTransaction();
	/**
	 * Suppression du modèle de service
	 */
	$del_Modele = $bdd_supervision->prepare('DELETE
			 FROM modele_service
			 WHERE ID_Modele_Service = :ID_Modele_Service');
	$del_Modele->execute(Array(
			'ID_Modele_Service' => htmlspecialchars($sID_Modele_Service)
	)) or die(print_r($del_Modele->errorInfo()));
	$del_Relation_Modele = $bdd_supervision->prepare('DELETE
			 FROM relation_modeles
			 WHERE ID_Modele_Service = :ID_Modele_Service');
	$del_Relation_Modele->execute(Array(
			'ID_Modele_Service' => htmlspecialchars($sID_Modele_Service)
	)) or die(print_r($del_Relation_Modele->errorInfo()));
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	http_response_code(500);
	die('Erreur suppression_modele_service: ' . $e->getMessage());
};

echo "Le modèle a bien été supprimé.";
