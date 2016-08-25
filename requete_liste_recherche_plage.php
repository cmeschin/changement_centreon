<?php
if (session_id()=='')
{
session_start();
};
include('log.php'); // chargement de la fonction de log
include_once('connexion_sql_centreon.php');
 
$lst_id_hote = (isset($_POST["slst_id_hote"])) ? $_POST["slst_id_hote"] : NULL;
try 
{
	$i = 1;
	$req_plage='';
	$req_plage = $bdd_centreon->prepare('SELECT
			 DISTINCT(Plage_Horaire) AS Nom_Plage,
			 Lundi,
			 Mardi,
			 Mercredi,
			 Jeudi,
			 Vendredi,
			 Samedi,
			 Dimanche
		FROM vInventaireServices AS vIS
			INNER JOIN host AS H ON vIS.host_id=H.host_id
		WHERE H.host_id IN (' . htmlspecialchars($lst_id_hote) . ')
		ORDER BY Plage_Horaire');
	$req_plage->execute(array()) or die(print_r($req_plage->errorInfo()));
	addlog("fonction chargerlistes_Complement_Plages...");
	addlog("SELECT DISTINCT(Plage_Horaire) AS Nom_Plage, Lundi, Mardi, Mercredi, Jeudi, Vendredi, Samedi, Dimanche FROM vInventaireServices AS vIS INNER JOIN host AS H ON vIS.host_id=H.host_id WHERE H.host_id IN (".$lst_id_hote.") ORDER BY Plage_Horaire");
		
	echo '<table id="T_Liste_Plage">';
	echo '<tr>';
	echo '<th>Sélection</th>';
	echo '<th>Plage Horaire</th>';
	echo '<th>Lundi</th>';
	echo '<th>Mardi</th>';
	echo '<th>Mercredi</th>';
	echo '<th>Jeudi</th>';
	echo '<th>Vendredi</th>';
	echo '<th>Samedi</th>';
	echo '<th>Dimanche</th>';
	echo '<!--		<th hidden>host_id</th>	-->';
	echo '</tr>';
	while ($res_plage = $req_plage->fetch())
	{ 
		addlog("Nom_plage" . $i ."=". htmlspecialchars($res_plage['Nom_Plage']));
		echo '<tr>';
		echo '<td><input type="checkbox" name="selection_plage" id="p' . $i . '"/></td>';
		echo '<td>' . htmlspecialchars($res_plage['Nom_Plage']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['lundi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['mardi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['mercredi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['jeudi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['vendredi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['samedi']) . '</td>';
		echo '<td>' . htmlspecialchars($res_plage['dimanche']) . '</td>';
		echo '</tr>';
		$i++; // incrément du compteur d'id
	};
} catch (Exception $e) 
{
	echo '</tr>';
	die('Erreur requete_liste_recherche_plage: ' . $e->getMessage());
};