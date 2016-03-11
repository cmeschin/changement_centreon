<?php
include_once('connexion_sql_supervision.php');
/**
 * Charger liste AM disponibles
 */
try {
	$req_lst_am_dispo = $bdd_supervision->prepare('SELECT
			 mbc_ba_id AS ba_id,
			 mbc_ba_nom AS ba_nom,
			 CONCAT(mbc_ba_id,"_",mbc_ba_nom) AS ba_concat
		 FROM mod_bam_centreon;');
	$req_lst_am_dispo->execute(Array()) or die(print_r($req_lst_am_dispo->errorInfo()));
	
} catch (Exception $e) {
	echo '<p>Echec chargement liste am dispo associé: ' . $e->getMessage() . '</p>';
	echo '<p>Essayer de recharger la page... si le problème persiste contacter l\'administrateur.</p>';
};

$nb_ligne = $req_lst_am_dispo->rowCount();
echo '<div id="div_am_dispo" class="liste_am">';
echo '<label for="am_dispo">Liste des AM disponibles ('. $nb_ligne .'):</label> <br/>';
echo '<select id="am_dispo" name="liste_am[]" size="10" multiple>';
while ($res_lst_am_dispo = $req_lst_am_dispo->fetch())
{ 
	echo '<option value="'. htmlspecialchars($res_lst_am_dispo['ba_concat']) . '">' . htmlspecialchars($res_lst_am_dispo['ba_nom']) . '</option>';
};
echo '</select> <br />';
echo '</div>';

echo '<div id="bouton_association_am" class="liste_am">';
echo '<button id="Associer_Selection" onclick="Associer_Selection_am()">Associer la sélection =></button> <br />';
echo '<button id="Dissocier_Selection" onclick="Dissocier_Selection_am()"><= Dissocier la sélection</button>';
echo '</div>';
echo '<div id="div_am_associe" class="liste_am">';
echo '<label for="am_associe">Liste des AM associées:</label> <br/>';
echo '<select id="am_associe" name="liste_am_associe[]" size="10" multiple>';
echo '</select> <br />';
echo '</div>';