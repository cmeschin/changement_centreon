<?php
if (session_id () == '') {
	session_start ();
};
header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).

$monclient = (isset($_POST["monclient"])) ? $_POST["monclient"] : NULL;
include_once('log.php');

if ($monclient )
{
    // récupérer la liste des hôtes de centreon et générer le tableau
	include_once('connexion_sql_centreon.php');
	try {
// correction le 11/01/15 suite à modification vue inventaire 
// 		$req_hote = $bdd_centreon->prepare('SELECT Distinct(host_name),
// 			 H.host_id,
// 			 host_alias,
// 			 host_address,
// 			 if(host_activate="1","actif","inactif") AS Controle,
// 			 Hote
// 			FROM vInventaireServices AS vIS
// 			INNER JOIN host AS H ON vIS.host_id=H.host_id
// 			WHERE Code_Client= :Code_Client
// 			ORDER BY H.host_name');
		$req_hote = $bdd_centreon->prepare('SELECT Distinct(Nom_Hote),
			 host_id,
			 Hote_Description,
			 IP_Hote,
			 Controle_Hote,
			 Hote
			FROM vInventaireServices
			WHERE Code_Client= :Code_Client
			ORDER BY Nom_Hote');
		$req_hote->execute(array(
				'Code_Client' => htmlspecialchars($monclient)
		)) or die(print_r($req_hote->errorInfo()));
	} catch (Exception $e) {
		die('Erreur requete_liste_hote: ' . $e->getMessage());
	};

	/**
	 * Construction de la liste des id_hote
	 */
	$res_elements_hote = $req_hote->fetchAll ();
	$_SESSION['lst_id_hote'] = "";
	addlog("Liste id_hote" . $_SESSION['lst_id_hote']);
	//while ($res_elements_hote = $req_elements_hote->fetch())
	foreach ( $res_elements_hote as $val_hote )
	{
		$_SESSION['lst_id_hote'] .= "," .$val_hote['host_id'];
	};
	$_SESSION['lst_id_hote'] = substr($_SESSION['lst_id_hote'],1); // chaine construite sans le premier caractère.
	addlog("Liste id_hote=" . $_SESSION['lst_id_hote']);
	
	echo '<p>Si un hôte n\'apparait pas dans la liste ci-dessous, c\'est qu\'il n\'est pas identifié pour la prestation actuelle.</p>';
	echo '<p>Utilisez la fonction de recherche ci-dessus pour vérifier son existence dans Centreon.</p>';
	echo '<p class="critique">Sélectionnez uniquement les hôtes dont vous souhaitez modifier les caractéristiques (Adresse IP, fonction, etc...) ou l\'état de la supervision (activation, désactivation, suppression).<br/>
			La désactivation ou la suppression d\'un hôte dans centreon implique la désactivation ou la suppression automatique des services qui lui sont associés.<br/>
			En revanche, l\'activation d\'un hôte réactivera automatiquement ses services <b>à l\'exception des services désactivés unitairement</b>. Il conviendra d\'être vigilant sur ce point.</p>';
	echo '<p>Inutile de sélectionner un hôte pour lequel vous souhaitez modifier un service; sélectionnez simplement le service correspondant dans la "Liste des services" ci-dessous.</p>';
	echo '<table id="T_Liste_Hote">';
		echo '<tr>';
		echo '<th>Sélection</th>';
		echo '<th>Localisation</th>';
		echo '<th>Type</th>';
		echo '<th>Hôte</th>';
		echo '<th>Description</th>';
		echo '<th>Adresse IP</th>';
		echo '<th>Controle</th>';
		echo '<th hidden="hidden">host_id</th>';
		echo '</tr>';
		$i = 1;
		foreach ( $res_elements_hote as $res_hote )
		{
//		while ($res_hote = $req_hote->fetch())
//		{ 
			$localisation = stristr($res_hote['Nom_Hote'],'-',1); // conserve la chaine avant le premier tiret
			$hote_type = stristr(substr(stristr($res_hote['Nom_Hote'],'-'),1),'-',1); // conserve la chaine entre les deux tirets
			$nom_hote = substr(stristr(substr(stristr($res_hote['Nom_Hote'],'-'),1),'-'),1); // enlève la localisation et la fonction et les deux -
			
			if (htmlspecialchars($res_hote['Controle_Hote']) == "inactif")
			{
				echo '<tr class="inactif">';
			} else
			{
				echo '<tr>';
			};
			echo '<td><input type="checkbox" name="selection_hote" id="' . $i . '"/></td>';
			echo '<td>' . htmlspecialchars($localisation) . '</td>';
			echo '<td>' . htmlspecialchars($hote_type) . '</td>';
			echo '<td>' . htmlspecialchars($nom_hote) . '</td>';
			echo '<td>' . htmlspecialchars($res_hote['Hote_Description']) . '</td>';
			echo '<td>' . htmlspecialchars($res_hote['IP_Hote']) . '</td>';
			//echo '<td>' . htmlspecialchars($res_hote['Controle']) . '</td>';
			echo '<td>' . htmlspecialchars($res_hote['Controle_Hote']) . '</td>';
			echo '<td hidden>h' . htmlspecialchars($res_hote['host_id']) . '</td>';
			echo '</tr>';
			$i ++;
		};
	echo '</table>';
} else 
{
    echo "ERREUR: Code_Client=[" . $monclient . "].";
};
