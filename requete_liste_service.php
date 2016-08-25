<?php
if (session_id () == '') {
	session_start ();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).

$monclient = (isset($_POST["monclient"])) ? $_POST["monclient"] : NULL;
if ($monclient ) 
{
	/**
	 *  récupérer la liste des services et générer le tableau
	 */
	include_once('connexion_sql_centreon.php');
	try 
	{
		/**
		 * Si liste id_host vide on la force à 0 pour ne pas avoir un message d'erreur.
		 */
		if ($_SESSION['lst_id_hote'] == "")
		{
			$_SESSION['lst_id_hote'] = 0;
		};
		
		$req_service = $bdd_centreon->prepare('SELECT
			 Distinct(Nom_Hote),
			 IP_Hote,
			 Controle,
			 Sonde,
			 Frequence,
			 Plage_Horaire,
			 host_id,
			 service_id
		FROM vInventaireServices
		WHERE (Code_Client= :prestation OR Code_Client LIKE "%INFRA%") AND host_id IN (' . htmlspecialchars($_SESSION['lst_id_hote']) . ')
		ORDER BY Nom_Hote,Sonde');
		$req_service->execute(array(
				'prestation' => htmlspecialchars($monclient)
		)) or die(print_r($req_service->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete_liste_service: ' . $e->getMessage());
	};
	$nom_hote = "";
	
	echo '<table id="T_Liste_Service">';
		echo '<tr>';
		echo '<th>Selection</th>';
		echo '<th>Hôte</th>';
		echo '<th>Service</th>';
		echo '<th>Fréquence</th>';
		echo '<th>Plage Horaire</th>';
		echo '<th>Controle</th>';
		echo '<th hidden="hidden">service_id</th>';
		echo '<th hidden="hidden">host_id</th>';
		echo '</tr>';
		$i = 1;

		while ($res_service = $req_service->fetch())
		{ 
			$nom_hote_actuel = substr(stristr(substr(stristr($res_service['Nom_Hote'],'-'),1),'-'),1);
			if ($nom_hote != $nom_hote_actuel)
			{
				$j = 1;
				$nom_hote = $nom_hote_actuel; // enlève la localisation et la fonction et les deux -
				$hote_localisation = stristr($res_service['Nom_Hote'],'-',1); // conserve la chaine avant le premier tiret
			};
			if (htmlspecialchars($res_service['Controle']) == "inactif") // mise en couleur pour les controles inactifs
			{
				echo '<tr class="inactif">';
			} else
			{
				echo '<tr>';
			};
			echo '<td><input type="checkbox" name="selection_service" id="s' . $i . '"/></td>';
			if ($j  == 1 || $j % 10 == 0){
				echo '<td title="' . htmlspecialchars($res_service['IP_Hote']) . ' - ' . $hote_localisation . '">' . $nom_hote . '</td>';
			}else
			{
				echo '<td></td>';
			};
			echo '<td>' . htmlspecialchars($res_service['Sonde']) . '</td>';
			echo '<td>' . htmlspecialchars($res_service['Frequence']) . '</td>';
			echo '<td>' . htmlspecialchars($res_service['Plage_Horaire']) . '</td>';
			echo '<td>' . htmlspecialchars($res_service['Controle']) . '</td>';
			echo '<td hidden>s' . htmlspecialchars($res_service['service_id']) . '</td>';
			echo '<td hidden>h' . htmlspecialchars($res_service['host_id']) . '</td>';
			echo '</tr>';
			$i++;
			$j++;
		};
	echo '</table>';
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};