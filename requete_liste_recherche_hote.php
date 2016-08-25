<?php
 header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include_once('connexion_sql_centreon.php');
$Hote = (isset($_POST["sSearch_Hote"])) ? $_POST["sSearch_Hote"] : NULL;
$Prestation = (isset($_POST["sClient"])) ? $_POST["sClient"] : NULL;
 
if (($Hote ) && ($Prestation)) 
{
    /**
     *  récupérer la liste des hôtes de Centreon et générer le tableau
     */
	try 
	{
		$req_recherche_hote = $bdd_centreon->prepare('SELECT
				 Distinct(Nom_Hote),
				 host_id,
				 Hote_Description,
				 IP_Hote,
				 Controle_Hote,
				 Hote 
			FROM vInventaireServices
			WHERE Code_Client <> :prestation
				 AND (Nom_Hote LIKE :Nom_Hote OR IP_Hote LIKE :IP_Hote)
			ORDER BY Nom_Hote LIMIT 15');

		$req_recherche_hote->execute(array(
				'prestation' => htmlspecialchars($Prestation),
				'Nom_Hote' => '%'.htmlspecialchars($Hote).'%',
				'IP_Hote' => '%'.htmlspecialchars($Hote).'%'
		)) or die(print_r($req_recherche_hote->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete_liste_recherche_hote: ' . $e->getMessage());
	};
	echo '<table id="T_Liste_Recherche_Hote">';
		echo '<tr>';
		echo '<th>Sélection</th>';
		echo '<th>Localisation</th>';
		echo '<th>Type</th>';
		echo '<th>Hôte</th>';
		echo '<th>Description</th>';
		echo '<th>Adresse IP</th>';
		echo '<th>Controle</th>';
		echo '<th hidden="hidden">Id_hote</th>'; 
		echo '</tr>'; 
		$i = 1;
		while ($res_recherche_hote = $req_recherche_hote->fetch())
		{ 
			$localisation = stristr($res_recherche_hote['Nom_Hote'],'-',1); // conserve la chaine avant le premier tiret
			$hote_type = stristr(substr(stristr($res_recherche_hote['Nom_Hote'],'-'),1),'-',1); // conserve la chaine entre les deux tirets
			$nom_hote = substr(stristr(substr(stristr($res_recherche_hote['Nom_Hote'],'-'),1),'-'),1); // enlève la localisation et la fonction et les deux -

			if (htmlspecialchars($res_recherche_hote['Controle_Hote']) == "inactif")
			{
				echo '<tr class="inactif" id="rh' . htmlspecialchars($res_recherche_hote['host_id']) . '" name="T_Liste_Recherche_Hote">';
			} else
			{
				echo '<tr id="rh' . htmlspecialchars($res_recherche_hote['host_id']) . '" name="T_Liste_Recherche_Hote">';
			};
				
			
			echo '<td><input type="checkbox" name="selection" id="' . $i . '"/></td>';
			echo '<td>' . htmlspecialchars($localisation) . '</td>';
			echo '<td>' . htmlspecialchars($hote_type) . '</td>';
			echo '<td>' . htmlspecialchars($nom_hote) . '</td>';
			echo '<td>' . htmlspecialchars($res_recherche_hote['Hote_Description']) . '</td>';
			echo '<td>' . htmlspecialchars($res_recherche_hote['IP_Hote']) . '</td>';
			echo '<td>' . htmlspecialchars($res_recherche_hote['Controle_Hote']) . '</td>';
			echo '<td hidden>' . htmlspecialchars($res_recherche_hote['host_id']) . '</td>';
			echo '</tr>';
		$i ++;
		};
	echo '</table>';
} else 
{
    echo "ERREUR: Le critère n'est pas sélectif! Hote=[" . $Hote . "]; Prestation=[" . $Client . "].";
};
 
