<?php
 header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
session_start();
//$ID_Modele = (isset($_POST["ID_Modele"])) ? $_POST["ID_Modele"] : NULL;
$sModele_Service = (isset($_POST["Modele_Service"])) ? $_POST["Modele_Service"] : NULL;

include_once('connexion_sql_supervision.php');

try {
	$req_liste_centreon = $bdd_supervision->prepare(
	//	'SELECT  MC.Service_id AS ID_Centreon, MC.Service_description AS Modele_Centreon
	//	FROM (modele_centreon AS MC LEFT JOIN relation_modeles AS RM ON MC.service_id = RM.ID_Modele_Service_Centreon) LEFT JOIN Modele_Service AS MS ON RM.ID_Modele_Service=MS.ID_Modele_Service
	//	WHERE RM.ID_Modele_Service_Centreon IS NULL OR MS.Modele_Service<> :Modele_Service'
	
		'SELECT MC.Service_id AS ID_Centreon,
			 MC.Service_description AS Modele_Centreon
		FROM modele_centreon AS MC 
		WHERE MC.Service_id NOT IN (SELECT ID_Modele_Service_Centreon FROM  relation_modeles)');
	$req_liste_centreon->execute(Array(
		'Modele_Service' => htmlspecialchars($sModele_Service)
	)) or die(print_r($req_liste_centreon->errorInfo()));
	
	$nb_ligne = $req_liste_centreon->rowCount();
} catch (Exception $e) {
	die('<p>Echec chargement liste modele Centreon: ' . $e->getMessage() . '</p> <br />
		<p>Essayer de recharger la page... si le problème persiste contacter l\'administrateur.</p>');
};

echo '<label for="Mod_Service_Centreon">Liste des modèles centreon ('. $nb_ligne .'):</label> <br/>';
echo '<select id="Mod_Service_Centreon" name="liste_modele[]" size="10" multiple>';

while ($res_liste_centreon = $req_liste_centreon->fetch())
{ 
	echo '<option value="'. htmlspecialchars($res_liste_centreon['ID_Centreon']) . '">' . htmlspecialchars($res_liste_centreon['Modele_Centreon']) . '</option>';
	
};
echo '</select> <br />';
echo '<button id="Associer_Selection" onclick="Associer_Selection()"><= Associer la sélection</button>';
		

						
						
