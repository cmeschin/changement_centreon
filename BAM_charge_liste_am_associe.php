<?php
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
//session_start();
//$ID_Modele = (isset($_POST["ID_Modele"])) ? $_POST["ID_Modele"] : NULL;
//$sModele_Service = (isset($_POST["Modele_Service"])) ? $_POST["Modele_Service"] : NULL;

include_once('connexion_sql_supervision.php');
try {
	//$req_liste_valeur = $bdd_supervision->prepare('SELECT MS.Modele_Service AS Modele_Service, MC.Service_Description AS Modele_Centreon, MC.Service_id AS ID_Centreon, MS.ID_Modele_Service AS ID_Modele_Service FROM ((Modele_Service AS MS INNER JOIN Relation_modeles AS RM ON MS.ID_Modele_Service=RM.ID_Modele_Service) LEFT JOIN modele_centreon AS MC ON RM.ID_Modele_Service_Centreon=MC.service_id) WHERE MS.Modele_Service= :Modele_Service');
	$req_lst_am_associe = $bdd_supervision->prepare('SELECT
			 mbc.mbc_ba_id AS ba_id,
			 mbc.mbc_ba_nom AS ba_nom
		 FROM mod_bam_centreon AS mbc
			 RIGHT JOIN gestion_bam_associe AS gba
			 ON mbc.mbc_ba_id=gba.gba_ba_id;');
	$req_lst_am_associe->execute(Array()) or die(print_r($req_lst_am_associe->errorInfo()));
	
} catch (Exception $e) {
	echo '<p>Echec chargement liste am associé: ' . $e->getMessage() . '</p>';
	echo '<p>Essayer de recharger la page... si le problème persiste contacter l\'administrateur.</p>';
};

$nb_ligne = $req_lst_am_associe->rowCount();
echo '<label for="am_dispo">Liste des AM associées ('. $nb_ligne .'):</label> <br/>';
echo '<select id="am_dispo" name="liste_am_associe[]" class="gb_config" size="10" multiple>';
while ($res_lst_am_associe = $req_lst_am_associe->fetch())
{ 
	echo '<option value="'. htmlspecialchars($res_lst_am_associe['ba_id']) . '">' . htmlspecialchars($res_lst_am_associe['ba_nom']) . '</option>';
};
echo '</select> <br />';
echo '<button id="Associer_Selection" onclick="Associer_Selection_am()">Associer la sélection =></button>';
		