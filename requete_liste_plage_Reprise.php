<?php
if (session_id()=='')
{
	session_start();
};
if ($monclient ) 
{
	/**
	 *  récupérer la liste des plages sélectionnés dans la demande en cours avec l'ID demande
	 */
	include('connexion_sql_supervision.php');
	try 
	{
		$req_plage_Dem = $bdd_supervision-> prepare('SELECT
			 ID_Periode_Temporelle,
			 ID_Demande,
			 Nom_Periode,
			 Lundi,
			 Mardi,
			 Mercredi,
			 Jeudi,
			 Vendredi,
			 Samedi,
			 Dimanche,
			 Commentaire,
			 Type_Action,
			 Etat_Parametrage,
			 selection
		FROM periode_temporelle
		 WHERE ID_Demande = :ID_Demande
		 ORDER BY Nom_Periode;');
		$req_plage_Dem->execute(array(
				'ID_Demande' => $_SESSION['ID_dem']
		)) or die(print_r($req_plage_Dem->errorInfo()));
	} catch (Exception $e) 
	{
		die('Erreur requete liste periode demande: ' . $e->getMessage());
	};
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
		echo '</tr>';
		$i = 1;
		while ($res_plage_Dem = $req_plage_Dem->fetch())
		{ 
			echo '<tr>';
				if ($res_plage_Dem['selection'] == "true")// si la plage horaire fait parti des modif à effectuer on la désactive
				{
					echo '<td><input disabled="disabled" type="checkbox" name="selection_plage" id="p' . $i . '"/></td>';
				} else
				{
					echo '<td><input type="checkbox" name="selection_plage" id="p' . $i . '"/></td>';
				};
			echo '<td>' . htmlspecialchars($res_plage_Dem['Nom_Periode']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Lundi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Mardi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Mercredi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Jeudi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Vendredi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Samedi']) . '</td>';
			echo '<td>' . htmlspecialchars($res_plage_Dem['Dimanche']) . '</td>';
			echo '</tr>';
			$i ++;
		};
	echo '</table>';
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};
 