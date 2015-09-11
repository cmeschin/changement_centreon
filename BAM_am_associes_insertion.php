<?php
 header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
session_start();
include('log.php'); // chargement de la fonction de log
$sListe_MS = (isset($_POST["Liste_MS"])) ? $_POST["Liste_MS"] : NULL;
$ID_Modele_Service=stristr(htmlspecialchars($sListe_MS),'$',TRUE);

$Liste_tmp = substr(stristr($sListe_MS,'$'),1);
// construit le tableau de valeur
if (strpos($Liste_tmp, '|') == 0)
{
	addlog("une seule valeur");
	$Liste_Associes[0] = $Liste_tmp;
}else
{
	addlog("plusieurs valeurs");
	$Liste_Associes = explode("|",$Liste_tmp);
};

include('connexion_sql_supervision.php'); 
try {
	$bdd_supervision->beginTransaction();
	$Nb_lig=count($Liste_Associes);
	addlog("tableau_listeasso=".print_r($Liste_Associes[0]));
	for ($i=0;$i < $Nb_lig;$i++)
	{
		$ins_modele_centreon = $bdd_supervision->prepare("INSERT INTO relation_modeles (ID_Modele_Service,ID_Modele_Service_Centreon) VALUES (:ID_Modele_Service,:ID_Modele_Service_Centreon)");
		$ins_modele_centreon->execute(Array(
			'ID_Modele_Service' => htmlspecialchars($ID_Modele_Service), // modele_Service
			'ID_Modele_Service_Centreon' => htmlspecialchars($Liste_Associes[$i]) // modele_centreon associé
		)) or die(print_r($ins_modele_centreon->errorInfo()));
	}; 
	$bdd_supervision->commit();
} catch (Exception $e) {
	$bdd_supervision->rollBack();
	die('Erreur ajout service associé: ' . $e->getMessage());
}
