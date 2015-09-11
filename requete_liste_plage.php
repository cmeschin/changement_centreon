<?php
if (session_id () == '') {
	session_start ();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).

$monclient = (isset($_POST["monclient"])) ? $_POST["monclient"] : NULL;
if ($monclient ) {
    // récupérer la liste des hôtes et générer le tableau
	include_once('connexion_sql_centreon.php');
	try {
		/**
		 * Si liste id_host vide on la force à 0 pour ne pas avoir un message d'erreur.
		 */
		if ($_SESSION['lst_id_hote'] == "")
		{
			$_SESSION['lst_id_hote'] = 0;
		};
		
		$req_plage = $bdd_centreon->prepare('SELECT
				 DISTINCT(Plage_Horaire) AS Nom_Plage,
				 Lundi,
				 Mardi,
				 Mercredi,
				 Jeudi,
				 Vendredi,
				 Samedi,
				 Dimanche
				 FROM vInventaireServices
				 WHERE (Code_Client = :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . htmlspecialchars($_SESSION['lst_id_hote']) . ')
				 ORDER BY Nom_Plage');
		$req_plage->execute(array(
				'prestation' => htmlspecialchars($monclient)
		)) or die(print_r($req_plage->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete_liste_plage: ' . $e->getMessage());
	};
	?>
	<p>Sélectionner les plages qui doivent être modifiées.</p>
	<p>Il convient toutefois de faire attention aux modifications sur les plages horaires car elles peuvent être utlisées par plusieurs services.</p>
	<p>Leur modification peut entrainer la génération d'alertes sur d'autres services qui les utiliserai.</p>
	<table id="T_Liste_Plage">
		<tr>
		<th>Sélection</th>
		<th>Plage Horaire</th>
		<th>Lundi</th>
		<th>Mardi</th>
		<th>Mercredi</th>
		<th>Jeudi</th>
		<th>Vendredi</th>
		<th>Samedi</th>
		<th>Dimanche</th>
		</tr> 
		<?php
		$i = 1;
		while ($res_plage = $req_plage->fetch())
		{ 
			echo '<tr>';
			echo '<td><input type="checkbox" name="selection_plage" id="p' . $i . '"/></td>';
			echo '<td >' . $res_plage['Nom_Plage'] . '</td>';
			echo '<td>' . $res_plage['lundi'] . '</td>';
			echo '<td>' . $res_plage['mardi'] . '</td>';
			echo '<td>' . $res_plage['mercredi'] . '</td>';
			echo '<td>' . $res_plage['jeudi'] . '</td>';
			echo '<td>' . $res_plage['vendredi'] . '</td>';
			echo '<td>' . $res_plage['samedi'] . '</td>';
			echo '<td>' . $res_plage['dimanche'] . '</td>';
			echo '</tr>';
			$i ++;
		};
		?>
	</table>
<?php
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};
 
