<?php
if (session_id()=='')
{
session_start();
};
// header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain).
include('log.php'); // chargement de la fonction de log
include_once('connexion_sql_centreon.php');
 
//$sID_Hote = (isset($_POST["sID_Hote"])) ? $_POST["sID_Hote"] : NULL;
//$sNbPlage = (isset($_POST["sNbPlage"])) ? $_POST["sNbPlage"] : NULL;
//$slst_nom_plage = (isset($_POST["slst_nom_plage"])) ? $_POST["slst_nom_plage"] : NULL;
$lst_id_hote = (isset($_POST["slst_id_hote"])) ? $_POST["slst_id_hote"] : NULL;
//echo "ID_Hote=". $sID_Hote;
//echo "NbPlage=". $sNbPlage;
//echo "lst_nom_plage=". $slst_nom_plage;
//echo "lst_id_hote=". $lst_id_hote;

//if ($sID_Hote && $sNbPlage) 
//{
try {
	$i = 1;
	$req_plage='';
//	$liste_plage='';
	//$liste_ID='';
//	if ($slst_nom_plage <> "vide")
//	{
//		$T_lst_nom_plage = explode("|",$slst_nom_plage);
//		$Nblst_plage=count($T_lst_nom_plage);
//		$T_lst_id = explode(",",$lst_id_hote);
		// reconstruction de la liste des id au format SQL
//		$NbID = count($T_lst_id); // compte le nombre d'id
//		for ($i=0; $i<$NbID;$i++)
// 		{
// 			$liste_ID += $T_lst_id[$i] . ',';
// 		};
// 		$liste_ID = rtrim($liste_ID,',');
// 		addlog("liste_id=".$liste_ID);
//	};
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
		$req_plage->execute(array(
				//'liste_id' => htmlspecialchars($lst_id_hote)
		)) or die(print_r($req_plage->errorInfo()));

		//echo print_r($req_plage);
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
	// vérifie l'existence d'une plage
	while ($res_plage = $req_plage->fetch())
	{ 
// 		$existant=false;
// 		if ($slst_nom_plage <> "vide")
// 		{ 
// 			for ($j = 0; $j < $Nblst_plage;$j++)
// 			{
// 				addlog("T_nom_plage=". $T_lst_nom_plage[$j]);
 				addlog("Nom_plage" . $i ."=". htmlspecialchars($res_plage['Nom_Plage']));
// 				//echo 'T_nom_plage='. $T_lst_nom_plage[$j];
// 				//echo 'Nom_plage='. htmlspecialchars($res_plage['Nom_Plage']);
// 				if ((htmlspecialchars($res_plage['Nom_Plage']) == $T_lst_nom_plage[$j]))
// 				{
// 					addlog('existant');
// 					$existant=true;
// 				};
// 			};
// 		};
// 		if ($existant==false)
// 		{
// 			$liste_plage = $liste_plage . '<input readonly="" type="checkbox" name="selection_plage" id="p' . $i . '"/>' . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['Nom_Plage']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['lundi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['mardi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['mercredi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['jeudi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['vendredi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['samedi']) . '!';
// 			$liste_plage = $liste_plage . htmlspecialchars($res_plage['dimanche']) . '|';
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
			//			echo '<td hidden>h' . htmlspecialchars($res_plage['host_id']) . '</td>';
			echo '</tr>';
			$i++; // incrément du compteur d'id
//		};
	};
	//mysql_close($bdd_centreon);
//	echo rtrim($liste_plage,'|');
} catch (Exception $e) {
	echo '</tr>';
	die('Erreur requete_liste_recherche_plage: ' . $e->getMessage());
};
	
//} else 
//{
//    echo "ERREUR: ID_Hote=[" . $sID_Hote . "]; NbPlage=[" . $sNbPlage . "]; lst_plage=[" . $slst_nom_plage . "].";
//};
