<?php
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
session_start();
$sModele_Service = (isset($_POST["Modele_Service"])) ? $_POST["Modele_Service"] : NULL;

include_once('connexion_sql_supervision.php');
try {
	$req_liste_associes = $bdd_supervision->prepare('SELECT
			 MC.Service_id AS ID_Centreon,
			 MC.Service_Description AS Modele_Centreon
			 FROM ((modele_service AS MS
				 INNER JOIN relation_modeles AS RM
					 ON MS.ID_Modele_Service=RM.ID_Modele_Service)
				 RIGHT JOIN modele_centreon AS MC
				 	ON RM.ID_Modele_Service_Centreon=MC.service_id)
			 WHERE MS.Modele_Service= :Modele_Service');
	$req_liste_associes->execute(Array(
		'Modele_Service' => $sModele_Service
		)) or die(print_r($req_liste_associes->errorInfo()));
	
} catch (Exception $e) {
	echo '<p>Echec chargement liste modele associé: ' . $e->getMessage() . '</p>';
	echo '<p>Essayer de recharger la page... si le problème persiste contacter l\'administrateur.</p>';
};

$nb_ligne = $req_liste_associes->rowCount();
echo '<label for="Mod_Service_Associes">Liste des modèles associés ('. $nb_ligne .'):</label> <br/>';
echo '<select id="Mod_Service_Associes" name="liste_associes[]" size="10" multiple>';
while ($res_liste_associes = $req_liste_associes->fetch())
{ 
	echo '<option value="'. htmlspecialchars($res_liste_associes['ID_Centreon']) . '">' . htmlspecialchars($res_liste_associes['Modele_Centreon']) . '</option>';
};
echo '</select> <br />';
echo '<button id="Dissocier_Selection" onclick="Dissocier_Selection()">Dissocier la sélection =></button>';
		